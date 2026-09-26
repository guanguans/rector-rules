<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */
declare(strict_types=1);

/**
 * Copyright (c) 2025-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/rector-rules
 */

namespace Guanguans\RectorRules\Rector\FuncCall;

use Guanguans\RectorRules\Rector\AbstractRector;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\Int_;
use Rector\PhpParser\Enum\NodeGroup;
use Rector\PhpParser\Node\BetterNodeFinder;
use Rector\PhpParser\Node\Value\ValueResolver;
use Rector\ValueObject\PhpVersionFeature;
use Rector\VersionBonding\Contract\MinPhpVersionInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;

/**
 * @see \Guanguans\RectorRulesTests\Rector\FuncCall\JsonThrowOnErrorRector\JsonThrowOnErrorRectorTest
 */
final class JsonThrowOnErrorRector extends AbstractRector implements MinPhpVersionInterface
{
    /** @readonly */
    private ValueResolver $valueResolver;

    /** @readonly */
    private BetterNodeFinder $betterNodeFinder;
    private bool $hasChanged = false;

    public function __construct(ValueResolver $valueResolver, BetterNodeFinder $betterNodeFinder)
    {
        $this->valueResolver = $valueResolver;
        $this->betterNodeFinder = $betterNodeFinder;
    }

    /**
     * @return list<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return NodeGroup::STMTS_AWARE;
    }

    /**
     * @see https://github.com/rectorphp/rector-src/blob/bc675f031ba86784696adb08f416548c9d4dc406/rules/Php73/Rector/FuncCall/JsonThrowOnErrorRector.php
     *
     * @param \PhpParser\Node\Expr\Closure|\PhpParser\Node\Stmt\Block|\PhpParser\Node\Stmt\Case_|\PhpParser\Node\Stmt\Catch_|\PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Declare_|\PhpParser\Node\Stmt\Do_|\PhpParser\Node\Stmt\Else_|\PhpParser\Node\Stmt\ElseIf_|\PhpParser\Node\Stmt\Finally_|\PhpParser\Node\Stmt\For_|\PhpParser\Node\Stmt\Foreach_|\PhpParser\Node\Stmt\Function_|\PhpParser\Node\Stmt\If_|\PhpParser\Node\Stmt\Namespace_|\PhpParser\Node\Stmt\TryCatch|\PhpParser\Node\Stmt\While_|\Rector\PhpParser\Node\FileNode $node
     */
    public function refactor(Node $node): ?Node
    {
        // if found, skip it :)
        $hasJsonErrorFuncCall = (bool) $this->betterNodeFinder->findFirst(
            $node,
            fn (Node $node): bool => $this->isNames($node, ['json_last_error', 'json_last_error_msg'])
        );

        if ($hasJsonErrorFuncCall) {
            return null;
        }

        $this->hasChanged = false;

        $this->traverseNodesWithCallable($node, function (Node $currentNode): ?FuncCall {
            if (!$currentNode instanceof FuncCall || $this->shouldSkipFuncCall($currentNode)) {
                return null;
            }

            if ($this->isName($currentNode, 'json_encode')) {
                return $this->processJsonEncode($currentNode);
            }

            if ($this->isName($currentNode, 'json_decode')) {
                return $this->processJsonDecode($currentNode);
            }

            return null;
        });

        if ($this->hasChanged) {
            return $node;
        }

        return null;
    }

    public function provideMinPhpVersion(): int
    {
        return PhpVersionFeature::JSON_EXCEPTION;
    }

    /**
     * @return list<\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample>
     */
    protected function codeSamples(): array
    {
        return [
            new CodeSample(
                <<<'PHP'
                    /** @noinspection ALL */
                    namespace Guanguans\RectorRulesTests\Rector\FuncCall\JsonThrowOnErrorRector\Fixture;

                    function jsonThrowOnError()
                    {
                        json_encode($content);
                        json_decode($json);

                        json_decode($json, true, 215);

                        json_decode($json, true, 122, JSON_THROW_ON_ERROR);
                    }
                    PHP,
                <<<'PHP'
                    /** @noinspection ALL */
                    namespace Guanguans\RectorRulesTests\Rector\FuncCall\JsonThrowOnErrorRector\Fixture;

                    function jsonThrowOnError()
                    {
                        json_encode($content, JSON_THROW_ON_ERROR);
                        json_decode($json, null, 512, JSON_THROW_ON_ERROR);

                        json_decode($json, true, 215, JSON_THROW_ON_ERROR);

                        json_decode($json, true, 122, JSON_THROW_ON_ERROR);
                    }
                    PHP
            ),
        ];
    }

    private function shouldSkipFuncCall(FuncCall $funcCall): bool
    {
        if ([] === $funcCall->args || $funcCall->isFirstClassCallable()) {
            return true;
        }

        foreach ($funcCall->args as $arg) {
            if (! $arg instanceof Arg) {
                continue;
            }

            if ($arg->name instanceof Identifier) {
                return true;
            }
        }

        return $this->isFirstValueStringOrArray($funcCall);
    }

    private function processJsonEncode(FuncCall $funcCall): ?FuncCall
    {
        if (isset($funcCall->args[1])) {
            return null;
        }

        $this->hasChanged = true;

        $funcCall->args[1] = new Arg($this->createConstFetch());

        return $funcCall;
    }

    private function processJsonDecode(FuncCall $funcCall): ?FuncCall
    {
        if (isset($funcCall->args[3])) {
            return null;
        }

        // set default to inter-args
        $funcCall->args[1] ??= new Arg($this->nodeFactory->createNull());

        $funcCall->args[2] ??= new Arg(new Int_(512));

        $this->hasChanged = true;
        $funcCall->args[3] = new Arg($this->createConstFetch());

        return $funcCall;
    }

    private function createConstFetch(): ConstFetch
    {
        return new ConstFetch(new Name('JSON_THROW_ON_ERROR'));
    }

    private function isFirstValueStringOrArray(FuncCall $funcCall): bool
    {
        if (! isset($funcCall->getArgs()[0])) {
            return false;
        }

        $firstArg = $funcCall->getArgs()[0];

        $value = $this->valueResolver->getValue($firstArg->value);

        if (\is_string($value)) {
            return true;
        }

        return \is_array($value);
    }
}
