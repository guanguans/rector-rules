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

namespace Guanguans\RectorRules\Rector\FunctionLike;

use Guanguans\RectorRules\Rector\AbstractRector;
use PhpParser\Node;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\FirstFindingVisitor;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\Comments\NodeDocBlock\DocBlockUpdater;
use Rector\DeadCode\NodeAnalyzer\ExprUsedInNodeAnalyzer;
use Rector\NodeManipulator\StmtsManipulator;
use Rector\PhpParser\Enum\NodeGroup;
use Rector\PhpParser\Node\BetterNodeFinder;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;

/**
 * @see \Guanguans\RectorRulesTests\Rector\FunctionLike\RenameGarbageVariableNameRector\RenameGarbageVariableNameRectorTest
 */
final class RenameGarbageVariableNameRector extends AbstractRector
{
    private const GARBAGE_VARIABLE_NAME = '_';
    private BetterNodeFinder $betterNodeFinder;
    private DocBlockUpdater $docBlockUpdater;
    private ExprUsedInNodeAnalyzer $exprUsedInNodeAnalyzer;
    private NodeFinder $nodeFinder;
    private PhpDocInfoFactory $phpDocInfoFactory;
    private StmtsManipulator $stmtsManipulator;

    public function __construct(
        BetterNodeFinder $betterNodeFinder,
        DocBlockUpdater $docBlockUpdater,
        ExprUsedInNodeAnalyzer $exprUsedInNodeAnalyzer,
        NodeFinder $nodeFinder,
        PhpDocInfoFactory $phpDocInfoFactory,
        StmtsManipulator $stmtsManipulator
    ) {
        $this->betterNodeFinder = $betterNodeFinder;
        $this->docBlockUpdater = $docBlockUpdater;
        $this->exprUsedInNodeAnalyzer = $exprUsedInNodeAnalyzer;
        $this->nodeFinder = $nodeFinder;
        $this->phpDocInfoFactory = $phpDocInfoFactory;
        $this->stmtsManipulator = $stmtsManipulator;
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
     * @param \PhpParser\Node\FunctionLike $node
     */
    public function refactor(Node $node): ?Node
    {
        return $node instanceof Foreach_
            ? $this->refactorForeach($node)
            : $this->refactorFunctionLike($node);
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

    private function refactorForeach(Foreach_ $foreachNode): ?Foreach_
    {
        if (
            !$foreachNode->keyVar instanceof Variable
            || !$foreachNode->valueVar instanceof Variable
            || !$this->isUsedVariable($foreachNode, $foreachNode->keyVar)
            || $this->isUsedVariable($foreachNode, $foreachNode->valueVar)
        ) {
            return null;
        }

        $hasChanged = false;

        if (self::GARBAGE_VARIABLE_NAME !== $foreachNode->valueVar->name) {
            $foreachNode->valueVar->name = self::GARBAGE_VARIABLE_NAME;
            $hasChanged = true;
        }

        return $hasChanged ? $foreachNode : null;
    }

    private function refactorFunctionLike(FunctionLike $node): ?FunctionLike
    {
        $lastParamNode = array_last($node->getParams());

        if (
            !$lastParamNode->var instanceof Variable
            || !$this->isUsedVariable($node, $lastParamNode->var)
        ) {
            return null;
        }

        $paramsNode = collect($node->getParams())
            ->slice(0, -1)
            ->filter(
                fn (Param $paramNode): bool => $paramNode->var instanceof Variable
                    && !$this->isUsedVariable($node, $paramNode->var)
            );

        $hasChanged = false;
        $newName = self::GARBAGE_VARIABLE_NAME;
        $phpDocInfo = $this->phpDocInfoFactory->createFromNode($node);

        foreach ($paramsNode as $paramNode) {
            if ($newName !== $paramNode->var->name) {
                $oldName = $paramNode->var->name;
                $paramNode->var->name = $newName;
                $hasChanged = true;

                if (!$phpDocInfo instanceof PhpDocInfo) {
                    continue;
                }

                $paramTagValues = $phpDocInfo->getPhpDocNode()->getParamTagValues();

                /**
                 * @see \Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector
                 */
                foreach ($paramTagValues as $paramTagValue) {
                    if ('$'.$oldName === $paramTagValue->parameterName) {
                        $paramTagValue->parameterName = '$'.$newName;
                        $phpDocInfo->removeByType(ParamTagValueNode::class, $oldName);
                        $this->docBlockUpdater->updateRefactoredNodeWithPhpDocInfo($node);
                    }
                }
            }

            $newName .= self::GARBAGE_VARIABLE_NAME;
        }

        return $hasChanged ? $node : null;
    }

    private function isUsedVariable(Node $node, Variable $variableNode): bool
    {
        // $variableName = $this->getName($variableNode);
        // $nodeTraverser = new NodeTraverser($firstFindingVisitor = new FirstFindingVisitor(
        //     function (Node $node) use ($variableName, $variableNode): bool {
        //         if (
        //             !$node instanceof Variable
        //             || !$this->isName($node, $variableName)
        //         ) {
        //             return false;
        //         }
        //
        //         return $node !== $variableNode;
        //     }
        // ));
        // $nodeTraverser->traverse([$node]);
        // return $firstFindingVisitor->getFoundNode();
        return (bool) $this->betterNodeFinder->findFirst(
            $node->stmts ?? [],
            fn (Node $subNode): bool => $this->exprUsedInNodeAnalyzer->isUsed($subNode, $variableNode)
        );
    }
}
