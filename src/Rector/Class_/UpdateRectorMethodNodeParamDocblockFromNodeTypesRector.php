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

namespace Guanguans\RectorRules\Rector\Class_;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;

/**
 * @see \Guanguans\RectorRulesTests\Rector\Class_\UpdateRectorMethodNodeParamDocblockFromNodeTypesRector\UpdateRectorMethodNodeParamDocblockFromNodeTypesRectorTest
 */
final class UpdateRectorMethodNodeParamDocblockFromNodeTypesRector extends AbstractUpdateClassMethodNodeParamDocblockFromNodeTypesRector
{
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

                    final class UpdateRectorMethodNodeParamDocblockFromNodeTypesRector extends AbstractRector
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

                    final class UpdateRectorMethodNodeParamDocblockFromNodeTypesRector extends AbstractRector
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
            ),
        ];
    }

    /**
     * @return class-string<\Rector\Rector\AbstractRector>
     */
    protected function classType(): string
    {
        return AbstractRector::class;
    }

    protected function classMethodNode(Class_ $classNode): ?ClassMethod
    {
        return $classNode->getMethod('refactor') ?? $classNode->getMethod('rawRefactor');
    }

    /**
     * @param \ReflectionClass<\Rector\Rector\AbstractRector> $reflectionClass
     *
     * @throws \ReflectionException
     *
     * @return list<class-string<\PhpParser\Node>>
     */
    protected function nodeTypes(\ReflectionClass $reflectionClass): array
    {
        return $reflectionClass->newInstanceWithoutConstructor()->getNodeTypes();
    }
}
