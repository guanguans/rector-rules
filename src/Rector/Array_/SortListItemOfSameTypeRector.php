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

namespace Guanguans\RectorRules\Rector\Array_;

use Guanguans\RectorRules\Rector\AbstractRector;
use Illuminate\Support\Collection;
use PhpParser\Node;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use Rector\PhpParser\Node\Value\ValueResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;

/**
 * @see \Guanguans\RectorRulesTests\Rector\Array_\SortListItemOfSameTypeRector\SortListItemOfSameTypeRectorTest
 */
final class SortListItemOfSameTypeRector extends AbstractRector
{
    private ValueResolver $valueResolver;

    public function __construct(ValueResolver $valueResolver)
    {
        $this->valueResolver = $valueResolver;
    }

    public function getNodeTypes(): array
    {
        return [
            Array_::class,
        ];
    }

    /**
     * @param \PhpParser\Node\Expr\Array_ $node
     */
    public function refactor(Node $node): ?Node
    {
        $keys = collect($node->items)
            ->pluck('key')
            ->map(fn (?Expr $expr) => $expr instanceof Expr ? $this->valueResolver->getValue($expr) : null);

        if (
            $keys->filter(static fn ($key): bool => null !== $key && !\is_int($key))->isNotEmpty()
            || !array_is_list(
                $keys
                    ->reduce(
                        static fn (Collection $carry, ?int $key): Collection => $carry->put(
                            $key ?? (int) $carry->keys()->sortDesc(\SORT_NUMERIC)->first(null, -1) + 1,
                            $key
                        ),
                        collect()
                    )
                    ->all()
            )
        ) {
            return null;
        }

        $values = collect($node->items)->pluck('value');

        if (
            $values
                ->map(static fn (Expr $exprNode): string => \get_class($exprNode))
                ->unique()
                ->count() > 1
            || $values
                ->map(fn (Expr $exprNode) => $this->valueResolver->getValue($exprNode))
                ->reject(static fn ($value): bool => null !== $value || !\is_scalar($value))
                ->isNotEmpty()
            || $values
                ->map(fn (Expr $exprNode) => $this->valueResolver->getValue($exprNode))
                ->map(static fn ($value): string => \gettype($value))
                ->unique()
                ->count() > 1
        ) {
            return null;
        }

        $newItems = collect($node->items)
            ->sort(function (ArrayItem $a, ArrayItem $b): int {
                $aValue = $this->valueResolver->getValue($a->value);
                $bValue = $this->valueResolver->getValue($b->value);

                if (\is_string($aValue) && \is_string($bValue)) {
                    return strcmp($aValue, $bValue);
                }

                return $aValue <=> $bValue;
            })
            ->all();

        if ($newItems === $node->items) {
            return null;
        }

        $node->items = $newItems;

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
                    [0 => 'foo', 1 => 'bar', 2 => 'baz'];
                    [0 => 'foo', 'bar', 2 => 'baz'];
                    PHP,
                <<<'PHP'
                    /** @noinspection ALL */
                    ['foo', 'bar', 'baz'];
                    ['foo', 'bar', 'baz'];
                    PHP,
            ),
        ];
    }
}
