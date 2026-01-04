<?php

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
use Illuminate\Support\Str;
use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\Comments\NodeDocBlock\DocBlockUpdater;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;

final class UpdateParamTypeOfRectorRefactorMethodRector extends AbstractRector
{
    private DocBlockUpdater $docBlockUpdater;
    private PhpDocInfoFactory $phpDocInfoFactory;

    public function __construct(
        DocBlockUpdater $docBlockUpdater,
        PhpDocInfoFactory $phpDocInfoFactory
    ) {
        $this->docBlockUpdater = $docBlockUpdater;
        $this->phpDocInfoFactory = $phpDocInfoFactory;
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
     * @throws \ReflectionException
     */
    public function refactor(Node $node): ?Node
    {
        $className = $this->getName($node);

        if (!is_subclass_of($className, \Rector\Rector\AbstractRector::class)) {
            return null;
        }

        /** @var \ReflectionClass<\Rector\Rector\AbstractRector> $reflectionClass */
        $reflectionClass = new \ReflectionClass($className);

        if (!$reflectionClass->isInstantiable()) {
            return null;
        }

        $hasUpdated = $this->updateDocBlock(
            collect($node->stmts)->first(
                fn (Stmt $stmt): bool => $stmt instanceof ClassMethod && $this->isName($stmt, 'refactor')
            ),
            $reflectionClass->newInstanceWithoutConstructor()->getNodeTypes()
        );

        return $hasUpdated ? $node : null;
    }

    /**
     * @throws \Symplify\RuleDocGenerator\Exception\PoorDocumentationException
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            $this->description(),
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
                        [
                            0 => 'foo',
                            1 => 'bar',
                            2 => 'baz',
                        ]
                        CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
                        [
                            'foo',
                            'bar',
                            'baz',
                        ]
                        CODE_SAMPLE,
                ),
            ],
        );
    }

    /**
     * @param list<class-string<\PhpParser\Node>> $nodeTypes
     */
    private function updateDocBlock(ClassMethod $node, array $nodeTypes): bool
    {
        Assert::allStringNotEmpty($nodeTypes);

        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($node);
        $tagName = 'param';
        $tagValue = \sprintf(
            '%s $node',
            collect($nodeTypes)->sort()->map(static fn (string $class): string => "\\$class")->implode('|')
        );

        $paramTagValueNode = $phpDocInfo->getParamTagValueByName('node');

        $hasUpdated = false;

        if (!$paramTagValueNode instanceof ParamTagValueNode) {
            $phpDocInfo->addPhpDocTagNode(new PhpDocTagNode("@$tagName", new GenericTagValueNode($tagValue)));
            $hasUpdated = true;
        }

        if (!Str::of((string) $paramTagValueNode)->endsWith($tagValue)) {
            $phpDocInfo->removeByName($tagName);
            $phpDocInfo->addPhpDocTagNode(new PhpDocTagNode("@$tagName", new GenericTagValueNode($tagValue)));
            $hasUpdated = true;
        }

        if ($hasUpdated) {
            $this->docBlockUpdater->updateRefactoredNodeWithPhpDocInfo($node);
        }

        return $hasUpdated;
    }
}
