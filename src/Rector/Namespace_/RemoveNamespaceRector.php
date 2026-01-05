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
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\FirstFindingVisitor;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use function Guanguans\RectorRules\Support\is_instance_of_any;

final class RemoveNamespaceRector extends AbstractRector
{
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
        if ($this->findFirstSkippedNode($node) instanceof Node) {
            return null;
        }

        if ($comments = $node->getComments()) {
            $nop = new Nop;
            $nop->setAttribute('comments', $comments);

            array_unshift($node->stmts, $nop, new Nop);
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
            ), new CodeSample(
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
                    namespace Guanguans\RectorRulesTests\Rector\Namespace_\RemoveNamespaceRector\Fixture;

                    it('is true', function (): void {
                        expect(true)->toBeTrue();
                    });
                    PHP,
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

                    it('is true', function (): void {
                        expect(true)->toBeTrue();
                    });
                    PHP,
            ),
        ];
    }

    /**
     * @see \PhpParser\NodeVisitor\
     * @see \PhpParser\NodeVisitor\FirstFindingVisitor
     *
     * @return null|\PhpParser\Node\Stmt\ClassLike|\PhpParser\Node\Stmt\Const_|\PhpParser\Node\Stmt\Function_
     */
    private function findFirstSkippedNode(Namespace_ $namespace): ?Node
    {
        $traverser = new NodeTraverser($firstFindingVisitor = new FirstFindingVisitor(
            static fn (Node $node): bool => is_instance_of_any($node, [
                /** ClassLike(Attribute、Class、Enum、Interface、Trait)、Constant、Function. */
                ClassLike::class,
                Const_::class,
                Function_::class,
            ])
        ));
        $traverser->traverse([$namespace]);

        return $firstFindingVisitor->getFoundNode();
    }
}
