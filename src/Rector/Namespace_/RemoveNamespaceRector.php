<?php

declare(strict_types=1);

/**
 * Copyright (c) 2025 guanguans<ityaozm@gmail.com>
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
use PhpParser\NodeVisitorAbstract;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use function Guanguans\RectorRules\Support\is_classes_of;

final class RemoveNamespaceRector extends AbstractRector
{
    /** ClassLike(Attribute、Class、Enum、Interface、Trait)、Constant、Function. */
    public const STMT_CLASSES = [
        ClassLike::class,
        Const_::class,
        Function_::class,
    ];

    /** @var list<\PhpParser\Node\Stmt\ClassLike|\PhpParser\Node\Stmt\Const_|\PhpParser\Node\Stmt\Function_> */
    private array $stmts = [];

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
        if ($this->collectStmts($node)) {
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
     * @throws \Symplify\RuleDocGenerator\Exception\PoorDocumentationException
     * @throws \Symplify\RuleDocGenerator\Exception\ShouldNotHappenException
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            $this->description(),
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
                        namespace Guanguans\ValetDriversTests\Support;

                        it('can get classes', function (): void {
                            expect(classes())->toBeArray()->toBeTruthy();
                        })->group(__DIR__, __FILE__);
                        CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
                        it('can get classes', function (): void {
                            expect(classes())->toBeArray()->toBeTruthy();
                        })->group(__DIR__, __FILE__);
                        CODE_SAMPLE,
                ),
            ],
        );
    }

    public function addStmt(Node $stmt): void
    {
        $this->stmts[] = $stmt;
    }

    public function refreshStmts(): void
    {
        $this->stmts = [];
    }

    /**
     * @noinspection SelfClassReferencingInspection
     *
     * @return list<\PhpParser\Node>
     */
    private function collectStmts(Namespace_ $namespace): array
    {
        $traverser = new NodeTraverser(
            new class($this) extends NodeVisitorAbstract {
                private RemoveNamespaceRector $rector;

                public function __construct(RemoveNamespaceRector $rector)
                {
                    $rector->refreshStmts();
                    $this->rector = $rector;
                }

                /**
                 * @noinspection PhpMissingParentCallCommonInspection
                 */
                public function enterNode(Node $node): void
                {
                    if (is_classes_of($node, RemoveNamespaceRector::STMT_CLASSES)) {
                        $this->rector->addStmt($node);
                    }
                }
            }
        );

        $traverser->traverse([$namespace]);

        // array_filter($namespace->stmts, static fn (Node $node): bool => is_classes_of($node, self::STMT_CLASSES));

        return $this->stmts;
    }
}
