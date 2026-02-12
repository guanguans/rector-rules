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

use Guanguans\RectorRules\Rector\AbstractProxyRector;
use PhpParser\Node;
use Rector\Renaming\Rector\FuncCall\RenameFunctionRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;

/**
 * @see \Guanguans\RectorRulesTests\Rector\FuncCall\RenameAppFunctionToResolveFunctionRector\RenameAppFunctionToResolveFunctionRectorTest
 */
final class RenameAppFunctionToResolveFunctionRector extends AbstractProxyRector
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

    /**
     * @see https://github.com/driftingly/rector-laravel/blob/main/src/Rector/FuncCall/AppToResolveRector.php
     * @see https://github.com/laravel/framework/commit/8f232a972fd8b4bfa1901a47c1e649e2a1278bd6
     *
     * @param \PhpParser\Node\Expr\FuncCall $node
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function rawRefactor(Node $node): ?Node
    {
        $abstract = $node->getArg('abstract', 0);

        if (null === $abstract || $this->getType($abstract->value)->isNull()->yes()) {
            return null;
        }

        $this->makeProxyRector()->configure([
            'app' => 'resolve',
        ]);

        return parent::rawRefactor($node);
    }

    protected function proxyRectorClass(): string
    {
        return RenameFunctionRector::class;
    }
}
