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
use PHPStan\PhpDocParser\Ast\Type\FullyQualifiedNameNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTypeChanger;
use Rector\BetterPhpDocParser\ValueObject\Type\FullyQualifiedIdentifierTypeNode;
use Webmozart\Assert\Assert;

abstract class AbstractUpdateClassMethodNodeParamDocblockFromNodeTypesRector extends AbstractRector
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

    final public function getNodeTypes(): array
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
    final public function refactor(Node $node): ?Node
    {
        $class = $this->getName($node);

        if (!is_subclass_of($class, $this->classType())) {
            return null;
        }

        $reflectionClass = new \ReflectionClass($class);

        if (!$reflectionClass->isInstantiable()) {
            return null;
        }

        $classMethodNode = $this->classMethodNode($node);

        if (!$classMethodNode instanceof ClassMethod) {
            return null;
        }

        $hasChanged = $this->changeNodeParamTypeOfRefactorMethod($classMethodNode, $this->nodeTypes($reflectionClass));

        return $hasChanged ? $node : null;
    }

    /**
     * @return class-string
     */
    abstract protected function classType(): string;

    abstract protected function classMethodNode(Class_ $classNode): ?ClassMethod;

    /**
     * @param \ReflectionClass<object> $reflectionClass
     *
     * @return list<class-string<\PhpParser\Node>>
     */
    abstract protected function nodeTypes(\ReflectionClass $reflectionClass): array;

    /**
     * @see \Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTypeChanger
     * @see \Rector\Config\Level\TypeDeclarationDocblocksLevel
     * @see \Rector\TypeDeclarationDocblocks\NodeDocblockTypeDecorator
     *
     * @param list<class-string<\PhpParser\Node>> $nodeTypes
     *
     * @throws \PHPStan\ShouldNotHappenException
     */
    private function changeNodeParamTypeOfRefactorMethod(ClassMethod $classMethodNode, array $nodeTypes): bool
    {
        // Assert::allIsInstanceOf($nodeTypes, Node::class);
        // Assert::allIsAOf($nodeTypes, Node::class);
        Assert::allSubclassOf($nodeTypes, Node::class);

        $this->phpDocTypeChanger->changeParamTypeNode(
            $classMethodNode,
            $this->phpDocInfoFactory->createFromNodeOrEmpty($classMethodNode),
            $classMethodNode->getParams()[0],
            'node',
            // new IdentifierTypeNode(
            //     collect($nodeTypes)->sort()->map(static fn (string $nodeType): string => "\\$nodeType")->implode('|')
            // )
            collect($nodeTypes)
                /** @see PhpdocTypesOrderFixer::sortTypes() */
                ->sort(\SORT_FLAG_CASE | \SORT_STRING)
                // ->map(static fn (string $nodeType): IdentifierTypeNode => new IdentifierTypeNode("\\$nodeType"))
                // ->map(static fn (string $nodeType): FullyQualifiedNameNode => new FullyQualifiedNameNode($nodeType))
                ->map(static fn (string $nodeType): FullyQualifiedIdentifierTypeNode => new FullyQualifiedIdentifierTypeNode($nodeType))
                ->pipe(
                    static fn (Collection $types): TypeNode => $types->count() > 1
                        ? new UnionTypeNode($types->all())
                        : $types->first()
                )
        );

        return true;
        // return $this->phpDocTypeChanger->changeParamType(
        //     $classMethodNode,
        //     $this->phpDocInfoFactory->createFromNodeOrEmpty($classMethodNode),
        //     collect($nodeTypes)
        //         /** @see PhpdocTypesOrderFixer::sortTypes() */
        //         ->sort(\SORT_FLAG_CASE | \SORT_STRING)
        //         ->map(static fn (string $nodeType): ObjectType => new ObjectType($nodeType))
        //         ->pipe(
        //             static fn (Collection $types): Type => $types->count() > 1
        //                 ? new UnionType($types->all())
        //                 : $types->first()
        //         ),
        //     $classMethodNode->getParams()[0],
        //     'node'
        // );
    }
}
