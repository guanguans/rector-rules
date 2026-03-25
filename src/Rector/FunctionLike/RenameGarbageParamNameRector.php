<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */
/** @noinspection EfferentObjectCouplingInspection */
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

namespace Guanguans\RectorRules\Rector\FunctionLike;

use Guanguans\RectorRules\Rector\AbstractRector;
use Illuminate\Support\Collection;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\Nop;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\Reflection\ClassReflection;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\BetterPhpDocParser\ValueObject\PhpDocAttributeKey;
use Rector\Comments\NodeDocBlock\DocBlockUpdater;
use Rector\DeadCode\NodeAnalyzer\ExprUsedInNodeAnalyzer;
use Rector\PhpParser\Enum\NodeGroup;
use Rector\PhpParser\Node\BetterNodeFinder;
use Rector\PHPStan\ScopeFetcher;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;

/**
 * @see \Guanguans\RectorRulesTests\Rector\FunctionLike\RenameGarbageParamNameRector\RenameGarbageParamNameRectorTest
 */
final class RenameGarbageParamNameRector extends AbstractRector
{
    private const GARBAGE_VARIABLE_NAME = '_';
    private BetterNodeFinder $betterNodeFinder;
    private DocBlockUpdater $docBlockUpdater;
    private ExprUsedInNodeAnalyzer $exprUsedInNodeAnalyzer;
    private PhpDocInfoFactory $phpDocInfoFactory;

    public function __construct(
        BetterNodeFinder $betterNodeFinder,
        DocBlockUpdater $docBlockUpdater,
        ExprUsedInNodeAnalyzer $exprUsedInNodeAnalyzer,
        PhpDocInfoFactory $phpDocInfoFactory
    ) {
        $this->betterNodeFinder = $betterNodeFinder;
        $this->docBlockUpdater = $docBlockUpdater;
        $this->exprUsedInNodeAnalyzer = $exprUsedInNodeAnalyzer;
        $this->phpDocInfoFactory = $phpDocInfoFactory;
    }

    public function getNodeTypes(): array
    {
        return [
            // ArrowFunction::class,
            // Closure::class,
            // NodeGroup::STMTS_AWARE,
            FunctionLike::class,
            Foreach_::class,
        ];
    }

    /**
     * @see \Rector\DeadCode\Rector\Assign\RemoveUnusedVariableAssignRector
     * @see \Rector\DeadCode\Rector\ClassMethod\RemoveUnusedConstructorParamRector
     * @see \Rector\DeadCode\Rector\Closure\RemoveUnusedClosureVariableUseRector
     * @see \Rector\DeadCode\Rector\Foreach_\RemoveUnusedForeachKeyRector
     * @see \Rector\DeadCode\Rector\If_\RemoveUnusedNonEmptyArrayBeforeForeachRector
     * @see \Rector\Php80\Rector\Catch_\RemoveUnusedVariableInCatchRector
     *
     * @param \PhpParser\Node\FunctionLike|\PhpParser\Node\Stmt\Foreach_ $node
     *
     * @throws \Rector\Exception\ShouldNotHappenException
     */
    public function refactor(Node $node): ?Node
    {
        return $node instanceof Foreach_ ? $this->refactorForeach($node) : $this->refactorFunctionLike($node);
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
                    collect($array)->filter(static function (string $value, int $key): bool {
                        return 2 === $key;
                    });

                    /**
                     * @param mixed $value
                     */
                    function filter($value, $key): bool
                    {
                        return 2 === $key;
                    }

                    /**
                     * @param mixed $key
                     */
                    function filter($value, $key): bool
                    {
                        return 2 === $key;
                    }

                    function array_is_list(array $array): bool
                    {
                        $nextKey = -1;

                        foreach ($array as $key => $value) {
                            if ($key !== ++$nextKey) {
                                return false;
                            }
                        }

                        return true;
                    }
                    PHP,
                <<<'PHP'
                    /** @noinspection ALL */
                    collect($array)->filter(static function (string $_, int $key): bool {
                        return 2 === $key;
                    });

                    /**
                     * @param mixed $_
                     */
                    function filter($_, $key): bool
                    {
                        return 2 === $key;
                    }

                    /**
                     * @param mixed $key
                     */
                    function filter($_, $key): bool
                    {
                        return 2 === $key;
                    }

                    function array_is_list(array $array): bool
                    {
                        $nextKey = -1;

                        foreach ($array as $key => $_) {
                            if ($key !== ++$nextKey) {
                                return false;
                            }
                        }

                        return true;
                    }
                    PHP,
            ),
        ];
    }

    /**
     * @throws \Rector\Exception\ShouldNotHappenException
     *
     * @noinspection PhpParamsInspection
     */
    private function refactorForeach(Foreach_ $foreachNode): ?Foreach_
    {
        // if (
        //     !$foreachNode->keyVar instanceof Variable
        //     || !$foreachNode->valueVar instanceof Variable
        //     || !$this->isUsedVariable($foreachNode, $foreachNode->keyVar)
        //     || $this->isUsedVariable($foreachNode, $foreachNode->valueVar)
        // ) {
        //     return null;
        // }
        //
        // $hasChanged = false;
        //
        // if (self::GARBAGE_VARIABLE_NAME !== $foreachNode->valueVar->name) {
        //     $foreachNode->valueVar->name = self::GARBAGE_VARIABLE_NAME;
        //     $hasChanged = true;
        // }
        //
        // return $hasChanged ? $foreachNode : null;

        // Convert Foreach_ to Closure to reuse refactorFunctionLike() logic.
        return $this->refactorFunctionLike(new Closure(
            [
                'params' => collect([new Param($foreachNode->valueVar)])
                    ->when(
                        $foreachNode->keyVar,
                        static fn (Collection $paramNodes, Expr $keyVarNode): Collection => $paramNodes->push(new Param($keyVarNode))
                    )
                    ->all(),
                'stmts' => $foreachNode->stmts,
            ],
            $foreachNode->getAttributes()
        )) ? $foreachNode : null;
    }

    /**
     * @throws \Rector\Exception\ShouldNotHappenException
     */
    private function refactorFunctionLike(FunctionLike $functionLikeNode): ?FunctionLike
    {
        $hasChanged = false;
        $docHasChanged = false;
        $newName = self::GARBAGE_VARIABLE_NAME;
        $phpDocInfo = $this->phpDocInfoFactory->createFromNode($functionLikeNode);

        foreach ($functionLikeNode->getParams() as $paramNode) {
            if (
                !$paramNode->var instanceof Variable
                || $paramNode->isPromoted()
                || $this->hasPrototypeMethod($functionLikeNode)
                || $this->isUsedVariable($functionLikeNode, $paramNode->var)
            ) {
                continue;
            }

            $name = $paramNode->var->name;

            if ($newName !== $name) {
                $paramNode->var->name = $newName;
                $hasChanged = true;
                $this->updateParamTagValueNodeParameterName($phpDocInfo, $name, $newName) and $docHasChanged = true;
            }

            $newName .= self::GARBAGE_VARIABLE_NAME;
        }

        $docHasChanged and $this->docBlockUpdater->updateRefactoredNodeWithPhpDocInfo($functionLikeNode);

        return $hasChanged ? $functionLikeNode : null;
    }

    /**
     * @throws \Rector\Exception\ShouldNotHappenException
     */
    private function hasPrototypeMethod(FunctionLike $functionLikeNode): bool
    {
        if (!$functionLikeNode instanceof ClassMethod) {
            return false;
        }

        $classReflection = ScopeFetcher::fetch($functionLikeNode)->getClassReflection();

        if (!$classReflection instanceof ClassReflection) {
            return false; // @codeCoverageIgnore
        }

        try {
            return $classReflection->getNativeReflection()->getMethod($this->getName($functionLikeNode))->hasPrototype();
        } catch (\ReflectionException $reflectionException) { // @codeCoverageIgnore
            return false; // @codeCoverageIgnore
        }
    }

    private function isUsedVariable(FunctionLike $node, Variable $variableNode): bool
    {
        // Skip abstract, interface, empty body function like and foreach.
        if (
            property_exists($node, 'stmts') && collect($node->stmts)->every(
                static fn (Node $stmtNode): bool => $stmtNode instanceof Nop
            )
        ) {
            return true;
        }

        return (bool) $this->betterNodeFinder->findFirst(
            $node,
            fn (Node $subNode): bool => $subNode !== $variableNode && $this->exprUsedInNodeAnalyzer->isUsed(
                $subNode,
                $variableNode
            )
        );
    }

    private function updateParamTagValueNodeParameterName(?PhpDocInfo $phpDocInfo, string $name, string $newName): bool
    {
        if (!$phpDocInfo instanceof PhpDocInfo) {
            return false;
        }

        $phpDocTagNodes = $phpDocInfo->getTagsByName('param');

        foreach ($phpDocTagNodes as $phpDocTagNode) {
            \assert($phpDocTagNode->value instanceof ParamTagValueNode);

            if ('$'.$name === $phpDocTagNode->value->parameterName) {
                $phpDocTagNode->value->parameterName = '$'.$newName;

                /** @see \Rector\BetterPhpDocParser\Printer\PhpDocInfoPrinter::printDocChildNode() */
                $phpDocTagNode->setAttribute(PhpDocAttributeKey::START_AND_END, null);

                return true;
            }
        }

        return false;
    }
}
