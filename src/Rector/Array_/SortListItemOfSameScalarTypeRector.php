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
 * @see \Guanguans\RectorRulesTests\Rector\Array_\SortListItemOfSameScalarTypeRector\SortListItemOfSameScalarTypeRectorTest
 */
final class SortListItemOfSameScalarTypeRector extends AbstractRector implements ConfigurableRectorInterface
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
        $this->rawConfigure([
            'ignore_comment' => true,
            'ignore_docblock' => true,
            'sort_comparator' => 'strnatcmp',
            'sort_direction' => 'asc',
        ]);
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
        if (
            collect($node->items)->contains(
                // Skip non-list
                fn (ArrayItem $arrayItemNode): bool => $arrayItemNode->key instanceof Expr
                    // If non-ignore-docblock, skip non-empty-docblock
                    || (!$this->configuration['ignore_docblock'] && $arrayItemNode->getDocComment() instanceof Doc)
                    // If non-ignore-comment, skip non-empty-comment
                    || (!$this->configuration['ignore_comment'] && $arrayItemNode->getComments())
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
                $this->getScalarArrayItemStringValue($a),
                $this->getScalarArrayItemStringValue($b)
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
        $this->rawConfigure($configuration, false);
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
                        'a10',
                        'a8',
                        'a9',
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
                        'a8',
                        'a9',
                        'a10',
                        'b',
                        'c',
                    ];
                    PHP,
                [
                    'ignore_comment' => false,
                    'ignore_docblock' => false,
                    // 'sort_comparator' => static fn (string $a, string $b): int => $a <=> $b,
                    // 'sort_comparator' => 'strcasecmp',
                    // 'sort_comparator' => 'strcmp',
                    // 'sort_comparator' => 'strnatcasecmp',
                    'sort_comparator' => 'strnatcmp',
                    // 'sort_direction' => 'desc',
                    'sort_direction' => 'asc',
                ]
            ),
        ];
    }

    /**
     * @throws \JsonException
     */
    private function getScalarArrayItemStringValue(ArrayItem $arrayItemNode): string
    {
        $value = $this->valueResolver->getValue($arrayItemNode->value);
        Assert::scalar($value);

        return \is_string($value) ? $value : json_encode($value, \JSON_THROW_ON_ERROR);
    }

    /**
     * @param array{
     *     ignore_comment: bool,
     *     ignore_docblock: bool,
     *     sort_comparator: callable(string, string): int,
     *     sort_direction: key-of<self::SORT_DIRECTION_MAP>,
     * } $configuration
     */
    private function rawConfigure(array $configuration, bool $isInitialized = true): void
    {
        if ($isInitialized) {
            Assert::keyExists($configuration, 'ignore_comment');
            Assert::keyExists($configuration, 'ignore_docblock');
            Assert::keyExists($configuration, 'sort_comparator');
            Assert::keyExists($configuration, 'sort_direction');
            $this->configuration = $configuration;
            $this->rawConfigure($configuration, false);

            return;
        }

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
        $this->comparator = fn (
            string $a,
            string $b
        ): int => self::SORT_DIRECTION_MAP[$this->configuration['sort_direction']]
            * ($this->configuration['sort_comparator'])($a, $b);
    }
}
