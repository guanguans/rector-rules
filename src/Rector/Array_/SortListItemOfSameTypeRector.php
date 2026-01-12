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
        // Skip non-list.
        if (
            collect($node->items)->contains(
                static fn (ArrayItem $arrayItemNode): bool => $arrayItemNode->key instanceof Expr
            )
        ) {
            return null;
        }

        $valueNodes = collect($node->items)->pluck('value');

        /** @noinspection NotOptimalIfConditionsInspection */
        if (
            // Skip non-same value node type.
            !$valueNodes
                ->map(static fn (Expr $exprNode): string => \get_class($exprNode))
                ->unique()
                // ->containsManyItems()
                ->containsOneItem()
            // Skip non-scalar value.
            || $valueNodes->contains(
                fn (Expr $exprNode): bool => !\is_scalar($this->valueResolver->getValue($exprNode))
            )
        ) {
            return null;
        }

        /** @var list<ArrayItem> $newItems */
        $newItems = collect($node->items)
            ->sort(fn (
                ArrayItem $a,
                ArrayItem $b
            ): int => $this->valueResolver->getValue($a->value) <=> $this->valueResolver->getValue($b->value))
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
                    [
                        'c',
                        'b',
                        'a',
                        'C',
                        'A',
                    ];
                    PHP,
                <<<'PHP'
                    /** @noinspection ALL */
                    [
                        'A',
                        'C',
                        'a',
                        'b',
                        'c',
                    ];
                    PHP,
            ),
        ];
    }
}
