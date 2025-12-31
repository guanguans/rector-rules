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
use Illuminate\Support\Str;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Const_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Nop;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;
use function Guanguans\RectorRules\Support\is_classes_of;

final class RemoveNamespaceRector extends AbstractRector implements ConfigurableRectorInterface
{
    /** 类(特性、枚举、注解)、函数、常量。 */
    public const STMT_CLASSES = [
        ClassLike::class,
        Function_::class,
        Const_::class,
    ];

    /** @var list<string> */
    private array $namespaces = [];
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
        if ([] !== $this->collectNodes($node)) {
            return null;
        }

        if (!Str::of($this->getName($node))->startsWith($this->namespaces)) {
            return null;
        }

        if ($comments = $node->getComments()) {
            $nop = new Nop;
            $nop->setAttribute('comments', $comments);

            array_unshift($node->stmts, $nop);
        }

        return array_reduce(
            $node->stmts,
            static function (array $stmts, Node $node): array {
                if ($node instanceof Expression) {
                    $stmts[] = new Nop;
                }

                $stmts[] = $node;

                return $stmts;
            },
            [new Nop]
        );
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
                new ConfiguredCodeSample(
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
                    [
                        'Guanguans\ValetDriversTests',
                    ],
                ),
            ],
        );
    }

    /**
     * @param list<string> $configuration
     */
    public function configure(array $configuration): void
    {
        Assert::allStringNotEmpty($configuration);
        $this->namespaces = $configuration;
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
     * @return list<Node>
     */
    private function collectNodes(Namespace_ $namespace): array
    {
        $traverser = new NodeTraverser(
            new class($this) extends NodeVisitorAbstract {
                private RemoveNamespaceRector $rector;

                public function __construct(RemoveNamespaceRector $rector)
                {
                    $this->rector = $rector;
                    $this->rector->refreshStmts();
                }

                public function enterNode(Node $node): void
                {
                    if (is_classes_of($node, RemoveNamespaceRector::STMT_CLASSES)) {
                        $this->rector->addStmt($node);
                    }
                }
            }
        );

        $traverser->traverse([$namespace]);

        return $this->stmts;
    }
}
