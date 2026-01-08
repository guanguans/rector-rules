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

namespace Guanguans\RectorRules\Rector\File;

use Guanguans\RectorRules\Rector\AbstractRector;
use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Namespace_;
use Rector\PhpParser\Node\FileNode;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;

final class SortFuncDefinitionsRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [
            FileNode::class,
        ];
    }

    /**
     * @param \Rector\PhpParser\Node\FileNode $node
     */
    public function refactor(Node $node): ?Node
    {
        $namespaceOrFileNode = collect($node->stmts)->first(
            static fn (Stmt $stmt): bool => $stmt instanceof Namespace_,
            $node
        );
        \assert($namespaceOrFileNode instanceof FileNode || $namespaceOrFileNode instanceof Namespace_);

        $classLikeNodeOrNull = collect($namespaceOrFileNode->stmts)->first(
            static fn (Stmt $stmt): bool => $stmt instanceof ClassLike
        );

        if ($classLikeNodeOrNull instanceof ClassLike) {
            return null;
        }

        // Find the location of the first function definition.
        $index = collect($namespaceOrFileNode->stmts)->search(fn (Stmt $stmt) => $this->parseFuncName($stmt));

        if (false === $index) {
            return null;
        }

        // Sort all stmts after the first function definition.
        $original = collect($namespaceOrFileNode->stmts)->skip($index + 1);
        $new = $original->sort(fn (Stmt $a, Stmt $b): int => strcasecmp(
            (string) $this->parseFuncName($a),
            (string) $this->parseFuncName($b)
        ));

        if ($original->all() === $new->all()) {
            return null;
        }

        // $namespaceOrFileNode->stmts = collect($namespaceOrFileNode->stmts)
        //     ->splice($index, $original->count(), $new->all())
        //     ->all();

        $namespaceOrFileNode->stmts = [
            ...collect($namespaceOrFileNode->stmts)->slice(0, $index)->all(),
            ...$new->all(),
        ];

        return $node;
    }

    /**
     * @return list<CodeSample>
     */
    protected function codeSamples(): array
    {
        return [
            new CodeSample(
                <<<'CODE_SAMPLE'
                    function b() {
                    }

                    if (! function_exists('a')) {
                        function a() {
                        }
                    }
                    CODE_SAMPLE,
                <<<'CODE_SAMPLE'
                    if (! function_exists('a')) {
                        function a() {
                        }
                    }

                    function b() {
                    }
                    CODE_SAMPLE
            ),
        ];
    }

    /**
     * 解析语句中的函数名称。支持直接定义的函数和通过 if 包裹的函数。
     */
    private function parseFuncName(Node $stmt): ?string
    {
        if ($stmt instanceof Function_) {
            return $this->getName($stmt);
        }

        if ($stmt instanceof If_) {
            foreach ($stmt->stmts as $innerStmt) {
                if ($innerStmt instanceof Function_) {
                    return $this->getName($innerStmt);
                }
            }
        }

        return null;
    }
}
