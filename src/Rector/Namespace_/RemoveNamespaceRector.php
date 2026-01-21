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

namespace Guanguans\RectorRules\Rector\Namespace_;

use Guanguans\RectorRules\Rector\AbstractRector;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Const_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Nop;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\PhpParser\Node\BetterNodeFinder;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;

/**
 * @see \Guanguans\RectorRulesTests\Rector\Namespace_\RemoveNamespaceRector\RemoveNamespaceRectorTest
 */
final class RemoveNamespaceRector extends AbstractRector
{
    private BetterNodeFinder $betterNodeFinder;

    public function __construct(BetterNodeFinder $betterNodeFinder)
    {
        $this->betterNodeFinder = $betterNodeFinder;
    }

    public function getNodeTypes(): array
    {
        return [
            Namespace_::class,
        ];
    }

    /**
     * @param \PhpParser\Node\Stmt\Namespace_ $node
     *
     * @return null|list<Node>
     */
    public function refactor(Node $node): ?array
    {
        if ($this->betterNodeFinder->hasInstancesOf($node, [
            /** ClassLike(Attribute、Class、Enum、Interface、Trait)、Constant、Function. */
            ClassLike::class,
            Const_::class,
            Function_::class,
        ])) {
            return null;
        }

        if ($comments = $node->getComments()) {
            $nopNode = new Nop;
            $nopNode->setAttribute(AttributeKey::COMMENTS, $comments);

            array_unshift($node->stmts, $nopNode, new Nop);
        }

        return $node->stmts;
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
                    namespace Guanguans\RectorRulesTests\Rector\Namespace_\RemoveNamespaceRector\Fixture;

                    it('is true', function (): void {
                        expect(true)->toBeTrue();
                    });
                    PHP,
                <<<'PHP'
                    /** @noinspection ALL */

                    it('is true', function (): void {
                        expect(true)->toBeTrue();
                    });
                    PHP,
            ),
        ];
    }
}
