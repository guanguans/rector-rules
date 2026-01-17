<?php

/** @noinspection ALL */
namespace Guanguans\RectorRules\Rector\Class_;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Rector\AbstractRector;

final class UpdateRectorRefactorParamDocblockFromNodeTypesRector extends AbstractUpdateClassMethodNodeParamDocblockFromNodeTypesRector
{
    protected function classType(): string
    {
        return AbstractRector::class;
    }

    protected function classMethodNode(Class_ $classNode): ?ClassMethod
    {
        return $classNode->getMethod('refactor');
    }

    protected function nodeTypes(\ReflectionClass $reflectionClass): array
    {
        return $reflectionClass->newInstanceWithoutConstructor()->getNodeTypes();
    }
}
