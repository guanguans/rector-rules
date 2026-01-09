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

namespace Guanguans\RectorRules\Rector\File;

use Guanguans\RectorRules\Rector\AbstractRector;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Namespace_;
use Rector\PhpParser\Node\FileNode;
use Rector\PhpParser\Node\Value\ValueResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;

final class SortFileFunctionStmtRector extends AbstractRector
{
    private ValueResolver $valueResolver;

    public function __construct(ValueResolver $valueResolver)
    {
        $this->valueResolver = $valueResolver;
    }

    public function getNodeTypes(): array
    {
        return [
            FileNode::class,
        ];
    }

    /**
     * @param \Rector\PhpParser\Node\FileNode $node
     */
    public function refactor(Node $node): ?Node
    {
        $rootNode = collect($node->stmts)->first(
            static fn (Stmt $stmtNode): bool => $stmtNode instanceof Namespace_,
            $node
        );
        \assert($rootNode instanceof FileNode || $rootNode instanceof Namespace_);

        if (
            collect($rootNode->stmts)->containsStrict(
                static fn (Stmt $stmtNode): bool => $stmtNode instanceof ClassLike
            )
            || !collect($rootNode->stmts)->containsStrict(
                static fn (Stmt $stmtNode): bool => $stmtNode instanceof Function_ || $stmtNode instanceof If_
            )
            || !collect($rootNode->stmts)->containsStrict(
                fn (Stmt $stmtNode): ?string => $this->parseFuncName($stmtNode)
            )
        ) {
            return null;
        }

        /** @var list<Stmt> $sortedStmts */
        $sortedStmts = collect($rootNode->stmts)
            ->sort(
                fn (Stmt $a, Stmt $b): int => ($aName = $this->parseFuncName($a)) && ($bName = $this->parseFuncName($b))
                    ? strcmp($aName, $bName)
                    : 0
            )
            // ->values()
            ->all();

        if ($rootNode->stmts === $sortedStmts) {
            return null;
        }

        $rootNode->stmts = $sortedStmts;

        return $node;
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
                    namespace Guanguans\RectorRulesTests\Rector\File\SortFileFunctionStmtRector\Fixture;

                    function c(): void {}
                    function b(): void {}
                    function a(): void {}
                    PHP,
                <<<'PHP'
                    /** @noinspection ALL */
                    namespace Guanguans\RectorRulesTests\Rector\File\SortFileFunctionStmtRector\Fixture;

                    function a(): void {}
                    function b(): void {}
                    function c(): void {}
                    PHP
            ),
        ];
    }

    /**
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     * @noinspection NotOptimalIfConditionsInspection
     */
    private function parseFuncName(Stmt $stmtNode): ?string
    {
        if ($stmtNode instanceof Function_) {
            return $this->getName($stmtNode);
        }

        if (
            $stmtNode instanceof If_
            && $stmtNode->cond instanceof BooleanNot
            && ($funcCallNode = $stmtNode->cond->expr) instanceof FuncCall
            && $funcCallNode->name instanceof Name
            && $this->isName($funcCallNode->name, 'function_exists')
            && $funcCallNode->args
            && $funcCallNode->args[0] instanceof Arg
            && $funcCallNode->args[0]->value instanceof String_
            && ($funcName = $this->valueResolver->getValue($funcCallNode->args[0]->value))
            && collect($stmtNode->stmts)->containsStrict(
                fn (Stmt $stmtNode): bool => $stmtNode instanceof Function_ && $this->isName($stmtNode, $funcName)
            )
        ) {
            return $funcName;
        }

        return null;
    }
}
