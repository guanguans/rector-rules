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
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\PhpParser\Node\Value\ValueResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Webmozart\Assert\Assert;

/**
 * @see \Guanguans\RectorRulesTests\Rector\Array_\SortListItemOfSameTypeRector\SortListItemOfSameTypeRectorTest
 */
final class SortListItemOfSameTypeRector extends AbstractRector implements ConfigurableRectorInterface
{
    private const SORT_DIRECTION_MAP = [
        'asc' => 1,
        'desc' => -1,
    ];

    /**
     * @var array{
     *     ignore_comment: bool,
     *     ignore_docblock: bool,
     *     sort_comparator: callable(string, string): int,
     *     sort_direction: key-of<self::SORT_DIRECTION_MAP>,
     * }
     */
    private array $configuration;

    /** @var callable(string, string): int */
    private $comparator;
    private ValueResolver $valueResolver;

    /**
     * @see \SORT_ASC
     * @see \SORT_DESC
     * @see \Ergebnis\Rector\Rules\Arrays\SortAssociativeArrayByKeyRector
     * @see \Symfony\Component\Finder\Iterator\SortableIterator
     */
    public function __construct(ValueResolver $valueResolver)
    {
        $this->configuration = [
            'ignore_comment' => true,
            'ignore_docblock' => true,
            'sort_comparator' => static fn (string $a, string $b): int => $a <=> $b,
            'sort_direction' => 'asc',
        ];
        $this->setComparator();
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
        // Skip non-list or non-empty-comments of array items.
        if (
            collect($node->items)->contains(
                fn (ArrayItem $arrayItemNode): bool => $arrayItemNode->key instanceof Expr
                    || (!$this->configuration['ignore_comment'] && $arrayItemNode->getComments())
                    || (!$this->configuration['ignore_docblock'] && $arrayItemNode->getDocComment() instanceof Doc)
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
            ->sort(fn (ArrayItem $a, ArrayItem $b): int => ($this->comparator)(
                (string) $this->valueResolver->getValue($a->value),
                (string) $this->valueResolver->getValue($b->value)
            ))
            ->all();

        if ($newItems === $node->items) {
            return null;
        }

        $node->items = $newItems;

        return $node;
    }

    /**
     * @param array{
     *     ignore_comment: bool,
     *     ignore_docblock: bool,
     *     sort_comparator: callable(string, string): int,
     *     sort_direction: key-of<self::SORT_DIRECTION_MAP>,
     * } $configuration
     */
    public function configure(array $configuration): void
    {
        foreach (array_keys($configuration) as $key) {
            Assert::keyExists($this->configuration, $key);
        }

        \array_key_exists('ignore_comment', $configuration) and Assert::boolean($configuration['ignore_comment']);
        \array_key_exists('ignore_docblock', $configuration) and Assert::boolean($configuration['ignore_docblock']);
        \array_key_exists('sort_comparator', $configuration) and Assert::isCallable($configuration['sort_comparator']);
        \array_key_exists('sort_direction', $configuration) and Assert::inArray(
            $configuration['sort_direction'],
            array_keys(self::SORT_DIRECTION_MAP)
        );

        $this->configuration = $configuration + $this->configuration;
        $this->setComparator();
    }

    /**
     * @throws \Symplify\RuleDocGenerator\Exception\ShouldNotHappenException
     *
     * @return list<\Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample>
     */
    protected function codeSamples(): array
    {
        return [
            new ConfiguredCodeSample(
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
                ['ignore_comment' => true, 'ignore_docblock' => true, 'sort_comparator' => static fn (string $a, string $b): int => $a <=> $b, 'sort_direction' => 'asc']
            ),
        ];
    }

    private function setComparator(): void
    {
        $this->comparator = fn (
            string $a,
            string $b
        ): int => self::SORT_DIRECTION_MAP[$this->configuration['sort_direction']]
            * ($this->configuration['sort_comparator'])($a, $b);
    }
}
