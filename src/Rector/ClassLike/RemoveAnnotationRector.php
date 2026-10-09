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

namespace Guanguans\RectorRules\Rector\ClassLike;

use Guanguans\RectorRules\Rector\AbstractRector;
use PhpParser\Node;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Property;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTagRemover;
use Rector\Comments\NodeDocBlock\DocBlockUpdater;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Webmozart\Assert\Assert;

/**
 * @see \Guanguans\RectorRulesTests\Rector\ClassLike\RemoveAnnotationRector\RemoveAnnotationRectorTest
 */
final class RemoveAnnotationRector extends AbstractRector implements ConfigurableRectorInterface
{
    /** @readonly */
    private PhpDocTagRemover $phpDocTagRemover;

    /** @readonly */
    private DocBlockUpdater $docBlockUpdater;

    /** @readonly */
    private PhpDocInfoFactory $phpDocInfoFactory;

    /** @var list<string> */
    private array $annotationsToRemove = [];

    public function __construct(PhpDocTagRemover $phpDocTagRemover, DocBlockUpdater $docBlockUpdater, PhpDocInfoFactory $phpDocInfoFactory)
    {
        $this->phpDocTagRemover = $phpDocTagRemover;
        $this->docBlockUpdater = $docBlockUpdater;
        $this->phpDocInfoFactory = $phpDocInfoFactory;
    }

    /**
     * @return list<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassConst::class, ClassLike::class, FunctionLike::class, Property::class];
    }

    /**
     * @see https://github.com/rectorphp/rector-src/blob/bc675f031ba86784696adb08f416548c9d4dc406/rules/DeadCode/Rector/ClassLike/RemoveAnnotationRector.php
     *
     * @param \PhpParser\Node\FunctionLike|\PhpParser\Node\Stmt\ClassConst|\PhpParser\Node\Stmt\ClassLike|\PhpParser\Node\Stmt\Property $node
     */
    public function refactor(Node $node): ?Node
    {
        Assert::notEmpty($this->annotationsToRemove);

        $phpDocInfo = $this->phpDocInfoFactory->createFromNode($node);

        if (!$phpDocInfo instanceof PhpDocInfo) {
            return null;
        }

        $hasChanged = false;

        foreach ($this->annotationsToRemove as $annotationToRemove) {
            $namedHasChanged = $this->phpDocTagRemover->removeByName($phpDocInfo, $annotationToRemove);

            if ($namedHasChanged) {
                $hasChanged = true;
            }

            if (!is_a($annotationToRemove, PhpDocTagValueNode::class, true)) {
                continue;
            }

            if ($phpDocInfo->removeByType($annotationToRemove)) {
                $hasChanged = true;
            }
        }

        if ($hasChanged) {
            $this->docBlockUpdater->updateRefactoredNodeWithPhpDocInfo($node);

            return $node;
        }

        return null;
    }

    /**
     * @param list<mixed> $configuration
     */
    public function configure(array $configuration): void
    {
        Assert::allString($configuration);

        $this->annotationsToRemove = $configuration;
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
                    /** @noinspection ALL */
                    namespace Guanguans\RectorRulesTests\Rector\ClassLike\RemoveAnnotationRector\Fixture;

                    /**
                     * @method getName()
                     */
                    final class Fixture
                    {
                    }
                    PHP,
                <<<'PHP'
                    /** @noinspection ALL */
                    namespace Guanguans\RectorRulesTests\Rector\ClassLike\RemoveAnnotationRector\Fixture;

                    final class Fixture
                    {
                    }
                    PHP,
                ['JMS\DiExtraBundle\Annotation\InjectParams', 'method'],
            ),
        ];
    }
}
