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
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Expr\Variable;
use Rector\Php\PhpVersionProvider;
use Rector\ValueObject\PhpVersionFeature;
use Rector\VersionBonding\Contract\MinPhpVersionInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;

/**
 * @see \Guanguans\RectorRulesTests\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector\ArraySpreadInsteadOfArrayMergeRectorTest
 */
final class ArraySpreadInsteadOfArrayMergeRector extends AbstractRector implements MinPhpVersionInterface
{
    /** @readonly */
    private PhpVersionProvider $phpVersionProvider;

    public function __construct(PhpVersionProvider $phpVersionProvider)
    {
        $this->phpVersionProvider = $phpVersionProvider;
    }

    /**
     * @return list<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    /**
     * @see https://github.com/rectorphp/rector-src/blob/bc675f031ba86784696adb08f416548c9d4dc406/rules/CodingStyle/Rector/FuncCall/ArraySpreadInsteadOfArrayMergeRector.php
     *
     * @param \PhpParser\Node\Expr\FuncCall $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($this->isName($node, 'array_merge')) {
            return $this->refactorArray($node);
        }

        return null;
    }

    public function provideMinPhpVersion(): int
    {
        return PhpVersionFeature::ARRAY_SPREAD;
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
                    namespace Guanguans\RectorRulesTests\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector\Fixture;

                    class IntegerKeys
                    {
                        public function run()
                        {
                            $iter1 = [0 => 'two', 3 => 'four'];
                            $iter2 = [5 => 'six', 7 => 'eight'];

                            return array_merge($iter1, $iter2);
                        }
                    }
                    PHP,
                <<<'PHP'
                    /** @noinspection ALL */
                    namespace Guanguans\RectorRulesTests\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector\Fixture;

                    class IntegerKeys
                    {
                        public function run()
                        {
                            $iter1 = [0 => 'two', 3 => 'four'];
                            $iter2 = [5 => 'six', 7 => 'eight'];

                            return [...$iter1, ...$iter2];
                        }
                    }
                    PHP
            ),
        ];
    }

    private function refactorArray(FuncCall $funcCall): ?Array_
    {
        if ($funcCall->isFirstClassCallable()) {
            return null;
        }

        $array = new Array_;

        foreach ($funcCall->args as $arg) {
            if (! $arg instanceof Arg) {
                continue;
            }

            // cannot handle unpacked arguments
            if ($arg->unpack) {
                return null;
            }

            $value = $arg->value;

            if ($this->shouldSkipArrayForInvalidKeys($value)) {
                return null;
            }

            if ($value instanceof Array_) {
                $array->items = [...$array->items, ...$value->items];

                continue;
            }

            $value = $this->resolveValue($value);
            $array->items[] = $this->createUnpackedArrayItem($value);
        }

        return $array;
    }

    private function shouldSkipArrayForInvalidKeys(Expr $expr): bool
    {
        $type = $this->getType($expr);

        if ($type->getIterableKeyType()->isInteger()->yes()) {
            // when on PHP 8.0+, pass non-array values already error on the first place
            // this check avoid unpack non-array values that cause error on php 7.4 as well,
            // @see https://3v4l.org/DuYHu#v7.4.33
            if (! $this->phpVersionProvider->isAtLeastPhpVersion(PhpVersionFeature::ARRAY_ON_ARRAY_MERGE)) {
                $nativeType = $this->nodeTypeResolver->getNativeType($expr);

                return ! $nativeType->isArray()
                    ->yes();
            }

            return false;
        }

        // php 8.1+ allow mixed key: int, string, and null
        return ! $this->phpVersionProvider->isAtLeastPhpVersion(PhpVersionFeature::ARRAY_SPREAD_STRING_KEYS);
    }

    private function resolveValue(Expr $expr): Expr
    {
        if ($expr instanceof FuncCall && $this->isIteratorToArrayFuncCall($expr)) {
            $arg = $expr->args[0];
            \assert($arg instanceof Arg);
            $expr = $arg->value;
            \assert($expr instanceof FuncCall);
        }

        if (
            !$expr instanceof Ternary
            || !$expr->cond instanceof FuncCall
            || !$this->isName($expr->cond, 'is_array')
        ) {
            return $expr;
        }

        if ($expr->if instanceof Variable && $this->isIteratorToArrayFuncCall($expr->else)) {
            return $expr->if;
        }

        return $expr;
    }

    private function createUnpackedArrayItem(Expr $expr): ArrayItem
    {
        return new ArrayItem($expr, null, false, [], true);
    }

    private function isIteratorToArrayFuncCall(Expr $expr): bool
    {
        if (
            !$expr instanceof FuncCall
            || !$this->isName($expr, 'iterator_to_array')
            || $expr->isFirstClassCallable()
        ) {
            return false;
        }

        return isset($expr->getArgs()[0]);
    }
}
