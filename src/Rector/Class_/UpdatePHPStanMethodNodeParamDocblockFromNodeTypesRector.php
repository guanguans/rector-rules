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

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Rules\Rule;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;

/**
 * @see \Guanguans\RectorRulesTests\Rector\Class_\UpdatePHPStanMethodNodeParamDocblockFromNodeTypesRector\UpdatePHPStanMethodNodeParamDocblockFromNodeTypesRectorTest
 */
final class UpdatePHPStanMethodNodeParamDocblockFromNodeTypesRector extends AbstractUpdateClassMethodNodeParamDocblockFromNodeTypesRector
{
    /**
     * @return list<\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample>
     */
    protected function codeSamples(): array
    {
        return [
            new CodeSample(
                <<<'PHP_WRAP'
                    /** @noinspection ALL */
                    namespace Guanguans\PHPStanRules\Rule;

                    use PhpParser\Node;
                    use PHPStan\Analyser\Scope;
                    use PHPStan\Node\FunctionLike;
                    use PHPStan\Rules\Rule;

                    final class ForbiddenSideEffectsFunctionLikeRule extends Rule
                    {
                        public function getNodeType(): string
                        {
                            return FunctionLike::class;
                        }

                        public function processNode(Node $node, Scope $scope): array
                        {
                            return [];
                        }
                    }
                    PHP_WRAP,
                <<<'PHP_WRAP'
                    /** @noinspection ALL */
                    namespace Guanguans\PHPStanRules\Rule;

                    use PhpParser\Node;
                    use PHPStan\Analyser\Scope;
                    use PHPStan\Node\FunctionLike;
                    use PHPStan\Rules\Rule;

                    final class ForbiddenSideEffectsFunctionLikeRule extends Rule
                    {
                        public function getNodeType(): string
                        {
                            return FunctionLike::class;
                        }

                        /**
                         * @param \PhpParser\Node\FunctionLike $node
                         */
                        public function processNode(Node $node, Scope $scope): array
                        {
                            return [];
                        }
                    }
                    PHP_WRAP,
            ),
        ];
    }

    /**
     * @return class-string<\PHPStan\Rules\Rule<\PhpParser\Node>>
     */
    protected function classType(): string
    {
        return Rule::class;
    }

    /**
     * @see https://github.com/guanguans/phpstan-rules/blob/main/src/Rule/AbstractMixedTypeRule.php
     */
    protected function classMethodNode(Class_ $classNode): ?ClassMethod
    {
        return $classNode->getMethod('rawProcessNode') ?? $classNode->getMethod('processNode');
    }

    /**
     * @param \ReflectionClass<\PHPStan\Rules\Rule<\PhpParser\Node>> $reflectionClass
     *
     * @throws \ReflectionException
     *
     * @return list<class-string<\PhpParser\Node>>
     */
    protected function nodeTypes(\ReflectionClass $reflectionClass): array
    {
        $rule = $reflectionClass->newInstanceWithoutConstructor();

        return method_exists($rule, 'getNodeTypes') ? $rule->getNodeTypes() : (array) $rule->getNodeType();
    }
}
