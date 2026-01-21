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

namespace Guanguans\RectorRules\Rector\FuncCall;

use Guanguans\RectorRules\Rector\AbstractRector;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use Rector\Renaming\Rector\FuncCall\RenameFunctionRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;

/**
 * @see \Guanguans\RectorRulesTests\Rector\FuncCall\RenameAppFunctionToResolveFunctionRector\RenameAppFunctionToResolveFunctionRectorTest
 */
final class RenameAppFunctionToResolveFunctionRector extends AbstractRector
{
    private RenameFunctionRector $renameFunctionRector;

    public function __construct(RenameFunctionRector $renameFunctionRector)
    {
        $this->renameFunctionRector = $renameFunctionRector;
        $this->renameFunctionRector->configure([
            'app' => 'resolve',
        ]);
    }

    public function getNodeTypes(): array
    {
        // return $this->renameFunctionRector->getNodeTypes();
        return [FuncCall::class];
    }

    /**
     * @see https://github.com/laravel/framework/commit/8f232a972fd8b4bfa1901a47c1e649e2a1278bd6
     *
     * @param \PhpParser\Node\Expr\FuncCall $node
     */
    public function refactor(Node $node): ?Node
    {
        return $node->getRawArgs() ? $this->renameFunctionRector->refactor($node) : null;
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
                    app();
                    app('request');
                    app(\Illuminate\Http\Request::class);
                    app(\Illuminate\Log\Logger::class, [
                        'logger' => new \Psr\Log\NullLogger(),
                    ]);
                    PHP,
                <<<'PHP'
                    /** @noinspection ALL */
                    app();
                    resolve('request');
                    resolve(\Illuminate\Http\Request::class);
                    resolve(\Illuminate\Log\Logger::class, [
                        'logger' => new \Psr\Log\NullLogger(),
                    ]);
                    PHP,
            ),
        ];
    }
}
