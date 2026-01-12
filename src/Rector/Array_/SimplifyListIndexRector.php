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
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Scalar\Int_;
use Rector\PhpParser\Node\Value\ValueResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;

/**
 * @see \Guanguans\RectorRulesTests\Rector\Array_\SimplifyListIndexRector\SimplifyListIndexRectorTest
 */
final class SimplifyListIndexRector extends AbstractRector
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
            ->map(
                fn (?Expr $exprNodeOrNull) => $exprNodeOrNull instanceof Expr
                    ? $this->valueResolver->getValue($exprNodeOrNull)
                    : null
            );

        if (
            $keys->filter(static fn ($key): bool => null !== $key && !\is_int($key))->isNotEmpty()
            || !array_is_list(
                $keys
                    ->reduce(
                        static fn (Collection $carry, ?int $key): Collection => $carry->put(
                            $key ?? (int) $carry->keys()->last(null, -1) + 1,
                            $key
                        ),
                        collect()
                    )
                    ->all()
            )
        ) {
            return null;
        }

        $hasChanged = false;

        foreach ($node->items as $arrayItemNode) {
            if ($arrayItemNode->key instanceof Int_) {
                $arrayItemNode->key = null;
                $hasChanged = true;
            }
        }

        return $hasChanged ? $node : null;
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
