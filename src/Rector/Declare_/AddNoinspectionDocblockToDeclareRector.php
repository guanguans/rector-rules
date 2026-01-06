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

namespace Guanguans\RectorRules\Rector\Declare_;

use Guanguans\RectorRules\Rector\AbstractRector;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PhpParser\Comment;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\Declare_;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Webmozart\Assert\Assert;

final class AddNoinspectionDocblockToDeclareRector extends AbstractRector implements ConfigurableRectorInterface
{
    /** @var array<non-empty-string, non-empty-list<string>> */
    private array $inspectionsMap = [];

    public function getNodeTypes(): array
    {
        return [
            Declare_::class,
        ];
    }

    /**
     * @param \PhpParser\Node\Stmt\Declare_ $node
     */
    public function refactor(Node $node): ?Node
    {
        if ([] === $this->getInspections()) {
            return null;
        }

        $commentContents = collect($node->getComments())
            ->map(static fn (Comment $comment): string => $comment->getText())
            ->implode(\PHP_EOL);

        $newComments = collect($this->getInspections())
            ->reduce(
                static fn (Collection $comments, string $inspection): Collection => str_contains(
                    $commentContents,
                    $inspection
                ) ? $comments : $comments->add(new Doc("/** @noinspection $inspection */")),
                collect($node->getComments())
            )
            ->sort(function (Comment $a, Comment $b): int {
                if (!$this->inspectionsContains($a) && $this->inspectionsContains($b)) {
                    return 1;
                }

                if ($this->inspectionsContains($a) && !$this->inspectionsContains($b)) {
                    return -1;
                }

                return strcmp($a->getText(), $b->getText());
            });

        if (
            $newComments
                ->map(static fn (Comment $comment): string => $comment->getText())
                ->implode(\PHP_EOL) === $commentContents
        ) {
            return null;
        }

        $node->setAttribute('comments', $newComments->all());

        return $node;
    }

    /**
     * @param array<non-empty-string, non-empty-list<string>> $configuration
     */
    public function configure(array $configuration): void
    {
        Assert::allIsArray($configuration);
        Assert::allNotEmpty($configuration);
        Assert::allStringNotEmpty(array_keys($configuration));

        $this->inspectionsMap = $configuration;
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
                    /** @noinspection AnonymousFunctionStaticInspection */
                    /** @noinspection StaticClosureCanBeUsedInspection */
                    /** @noinspection ALL */
                    /** @noinspection PhpUnusedAliasInspection */
                    declare(strict_types=1);
                    PHP,
                <<<'PHP'
                    /** @noinspection AnonymousFunctionStaticInspection */
                    /** @noinspection NullPointerExceptionInspection */
                    /** @noinspection PhpPossiblePolymorphicInvocationInspection */
                    /** @noinspection PhpUndefinedClassInspection */
                    /** @noinspection PhpUnhandledExceptionInspection */
                    /** @noinspection PhpVoidFunctionResultUsedInspection */
                    /** @noinspection StaticClosureCanBeUsedInspection */
                    /** @noinspection ALL */
                    /** @noinspection PhpUnusedAliasInspection */
                    declare(strict_types=1);
                    PHP,
                [
                    '*/Fixture/fixture.php' => [
                        'AnonymousFunctionStaticInspection',
                        'StaticClosureCanBeUsedInspection'],
                    '*/fixture.php' => [
                        'NullPointerExceptionInspection',
                        'PhpPossiblePolymorphicInvocationInspection',
                        'PhpUndefinedClassInspection',
                        'PhpUnhandledExceptionInspection',
                        'PhpVoidFunctionResultUsedInspection',
                    ],
                    '*/skip_same_inspections.php' => [
                        'ALL',
                    ],
                ],
            ),
        ];
    }

    private function inspectionsContains(Comment $comment): bool
    {
        foreach ($this->getInspections() as $inspection) {
            if (str_contains($comment->getText(), $inspection)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return list<string>
     */
    private function getInspections(): array
    {
        /** @var array<non-empty-string, list<string>> $inspectionsMap */
        static $inspectionsMap = [];

        $inspectionsMap[$this->file->getFilePath()] ??= collect($this->inspectionsMap)
            ->filter(fn (array $inspections, string $path) => Str::is($path, $this->file->getFilePath()))
            // ->flatten()
            ->collapse()
            ->unique()
            // ->sort()
            // ->values()
            // ->dd()
            ->all();

        return $inspectionsMap[$this->file->getFilePath()];
    }
}
