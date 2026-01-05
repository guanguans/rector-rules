<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */
/** @noinspection PhpUnusedAliasInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2025-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/rector-rules
 */

namespace Guanguans\RectorRules\Rector\Class_;

use Guanguans\RectorRules\Rector\AbstractRector;
use Illuminate\Support\Collection;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTypeChanger;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Webmozart\Assert\Assert;

final class UpdateRectorRefactorParamDocblockFromNodeTypesRector extends AbstractRector
{
    private PhpDocInfoFactory $phpDocInfoFactory;
    private PhpDocTypeChanger $phpDocTypeChanger;

    public function __construct(
        PhpDocInfoFactory $phpDocInfoFactory,
        PhpDocTypeChanger $phpDocTypeChanger
    ) {
        $this->phpDocInfoFactory = $phpDocInfoFactory;
        $this->phpDocTypeChanger = $phpDocTypeChanger;
    }

    public function getNodeTypes(): array
    {
        return [
            Class_::class,
        ];
    }

    /**
     * @param \PhpParser\Node\Stmt\Class_ $node
     *
     * @throws \PHPStan\ShouldNotHappenException
     * @throws \ReflectionException
     */
    public function refactor(Node $node): ?Node
    {
        $class = $this->getName($node);

        if (!is_subclass_of($class, \Rector\Rector\AbstractRector::class)) {
            return null;
        }

        /** @var \ReflectionClass<\Rector\Rector\AbstractRector> $reflectionClass */
        $reflectionClass = new \ReflectionClass($class);

        if (!$reflectionClass->isInstantiable()) {
            return null;
        }

        $hasChanged = $this->changeParamTypeOfNode(
            $node->getMethod('refactor'),
            $reflectionClass->newInstanceWithoutConstructor()->getNodeTypes()
        );

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
                    namespace Guanguans\RectorRules\Rector\Class_;

                    use Guanguans\RectorRules\Rector\AbstractRector;
                    use PhpParser\Node;
                    use PhpParser\Node\Stmt\Class_;

                    final class UpdateRectorRefactorParamDocblockFromNodeTypesRector extends AbstractRector
                    {
                        public function getNodeTypes(): array
                        {
                            return [
                                Class_::class,
                            ];
                        }

                        public function refactor(Node $node): ?Node
                        {
                            return null;
                        }
                    }
                    PHP,
                <<<'PHP'
                    /** @noinspection ALL */
                    namespace Guanguans\RectorRules\Rector\Class_;

                    use Guanguans\RectorRules\Rector\AbstractRector;
                    use PhpParser\Node;
                    use PhpParser\Node\Stmt\Class_;

                    final class UpdateRectorRefactorParamDocblockFromNodeTypesRector extends AbstractRector
                    {
                        public function getNodeTypes(): array
                        {
                            return [
                                Class_::class,
                            ];
                        }

                        /**
                         * @param \PhpParser\Node\Stmt\Class_ $node
                         */
                        public function refactor(Node $node): ?Node
                        {
                            return null;
                        }
                    }
                    PHP,
            ), new CodeSample(
                <<<'PHP'
                    /** @noinspection ALL */
                    namespace Guanguans\RectorRules\Rector\Name;

                    use Guanguans\RectorRules\Rector\AbstractRector;
                    use PhpParser\Node;
                    use PhpParser\Node\Expr\FuncCall;
                    use PhpParser\Node\Expr\Variable;
                    use PhpParser\Node\Identifier;
                    use PhpParser\Node\Name;

                    final class RenameToPsrNameRector extends AbstractRector
                    {
                        public function getNodeTypes(): array
                        {
                            return [
                                FuncCall::class,
                                Identifier::class,
                                Name::class,
                                Variable::class,
                            ];
                        }

                        public function refactor(Node $node): ?Node
                        {
                            return null;
                        }
                    }
                    PHP,
                <<<'PHP'
                    /** @noinspection ALL */
                    namespace Guanguans\RectorRules\Rector\Name;

                    use Guanguans\RectorRules\Rector\AbstractRector;
                    use PhpParser\Node;
                    use PhpParser\Node\Expr\FuncCall;
                    use PhpParser\Node\Expr\Variable;
                    use PhpParser\Node\Identifier;
                    use PhpParser\Node\Name;

                    final class RenameToPsrNameRector extends AbstractRector
                    {
                        public function getNodeTypes(): array
                        {
                            return [
                                FuncCall::class,
                                Identifier::class,
                                Name::class,
                                Variable::class,
                            ];
                        }

                        /**
                         * @param \PhpParser\Node\Expr\FuncCall|\PhpParser\Node\Expr\Variable|\PhpParser\Node\Identifier|\PhpParser\Node\Name $node
                         */
                        public function refactor(Node $node): ?Node
                        {
                            return null;
                        }
                    }
                    PHP,
            ),
        ];
    }

    /**
     * @see \Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTypeChanger
     * @see \Rector\Config\Level\TypeDeclarationDocblocksLevel
     * @see \Rector\TypeDeclarationDocblocks\NodeDocblockTypeDecorator
     *
     * @param list<class-string<\PhpParser\Node>> $nodeTypes
     *
     * @throws \PHPStan\ShouldNotHappenException
     */
    private function changeParamTypeOfNode(ClassMethod $node, array $nodeTypes): bool
    {
        // Assert::allIsInstanceOf($nodeTypes, Node::class);
        // Assert::allIsAOf($nodeTypes, Node::class);
        Assert::allSubclassOf($nodeTypes, Node::class);

        // $this->phpDocTypeChanger->changeParamTypeNode(
        //     $node,
        //     $this->phpDocInfoFactory->createFromNodeOrEmpty($node),
        //     $node->getParams()[0],
        //     'node',
        //     new IdentifierTypeNode(
        //         collect($nodeTypes)->sort()->map(static fn (string $class): string => "\\$class")->implode('|')
        //     )
        // );

        return $this->phpDocTypeChanger->changeParamType(
            $node,
            $this->phpDocInfoFactory->createFromNodeOrEmpty($node),
            collect($nodeTypes)
                ->sort()
                ->map(static fn (string $class): ObjectType => new ObjectType($class))
                ->pipe(
                    static fn (Collection $types): Type => $types->count() > 1
                        ? new UnionType($types->all())
                        : $types->first()
                ),
            $node->getParams()[0],
            'node'
        );
    }
}
