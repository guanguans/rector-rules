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
use PhpParser\Comment;
use PhpParser\Node;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\PhpParser\Node\FileNode;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;

/**
 * @see \Guanguans\RectorRulesTests\Rector\File\SortFileFirstStmtDocblockRector\SortFileFirstStmtDocblockRectorTest
 */
final class SortFileFirstStmtDocblockRector extends AbstractRector
{
    private const NOINSPECTION = '@noinspection';

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
        if ([] === $node->stmts) {
            return null;
        }

        $stmtNode = $node->stmts[0];
        $comments = $stmtNode->getComments();
        $newComments = collect($comments)
            ->sort(static function (Comment $a, Comment $b): int {
                if (str_contains($a->getText(), self::NOINSPECTION) && !str_contains($b->getText(), self::NOINSPECTION)) {
                    return -1;
                }

                if (!str_contains($a->getText(), self::NOINSPECTION) && str_contains($b->getText(), self::NOINSPECTION)) {
                    return 1;
                }

                return strcmp($a->getText(), $b->getText());
            })
            ->all();

        if ($newComments === $comments) {
            return null;
        }

        $stmtNode->setAttribute(AttributeKey::COMMENTS, $newComments);

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

                    /**
                     * Copyright (c) 2025-2026 guanguans<ityaozm@gmail.com>
                     *
                     * For the full copyright and license information, please view
                     * the LICENSE file that was distributed with this source code.
                     *
                     * @see https://github.com/guanguans/rector-rules
                     */

                    /** @noinspection StaticClosureCanBeUsedInspection */
                    /** @noinspection NullPointerExceptionInspection */
                    /** @noinspection PhpPossiblePolymorphicInvocationInspection */
                    declare(strict_types=1);
                    PHP,
                <<<'PHP'
                    /** @noinspection ALL */
                    /** @noinspection NullPointerExceptionInspection */
                    /** @noinspection PhpPossiblePolymorphicInvocationInspection */
                    /** @noinspection StaticClosureCanBeUsedInspection */
                    /**
                     * Copyright (c) 2025-2026 guanguans<ityaozm@gmail.com>
                     *
                     * For the full copyright and license information, please view
                     * the LICENSE file that was distributed with this source code.
                     *
                     * @see https://github.com/guanguans/rector-rules
                     */
                    declare(strict_types=1);
                    PHP,
            ),
        ];
    }
}
