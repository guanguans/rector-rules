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

namespace Guanguans\RectorRules\Rector\ClassMethod;

use Guanguans\RectorRules\Rector\AbstractRector;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Reflection\ClassReflection;
use Rector\PHPStan\ScopeFetcher;
use Rector\Privatization\NodeManipulator\VisibilityManipulator;
use Rector\ValueObject\Visibility;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;

/**
 * @see \Guanguans\RectorRulesTests\Rector\ClassMethod\PrivateToProtectedVisibilityForTraitRector\PrivateToProtectedVisibilityForTraitRectorTest
 */
final class PrivateToProtectedVisibilityForTraitRector extends AbstractRector
{
    private VisibilityManipulator $visibilityManipulator;

    public function __construct(VisibilityManipulator $visibilityManipulator)
    {
        $this->visibilityManipulator = $visibilityManipulator;
    }

    public function getNodeTypes(): array
    {
        return [
            ClassMethod::class,
        ];
    }

    /**
     * @see \Rector\Visibility\Rector\ClassMethod\ChangeMethodVisibilityRector
     *
     * @param \PhpParser\Node\Stmt\ClassMethod $node
     *
     * @throws \PHPStan\Reflection\MissingMethodFromReflectionException
     * @throws \Rector\Exception\ShouldNotHappenException
     */
    public function refactor(Node $node): ?Node
    {
        // $classReflection = ScopeFetcher::fetch($node)->getTraitReflection();
        $classReflection = ScopeFetcher::fetch($node)->getClassReflection();

        if (
            !$classReflection instanceof ClassReflection
            || !$classReflection->isTrait()
            // || !$classReflection->getMethod($this->getName($node), ScopeFetcher::fetch($node))->isPrivate()
            || !$classReflection->getNativeMethod($this->getName($node))->isPrivate()
        ) {
            return null;
        }

        $this->visibilityManipulator->changeNodeVisibility($node, Visibility::PROTECTED);

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
                    trait Foo
                    {
                        private function run(): void
                        {
                        }
                    }
                    PHP,
                <<<'PHP'
                    /** @noinspection ALL */
                    trait Foo
                    {
                        protected function run(): void
                        {
                        }
                    }
                    PHP,
            ),
        ];
    }
}
