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

namespace Guanguans\RectorRules\Rector\Scalar;

use Guanguans\RectorRules\Rector\AbstractProxyRector;
use GuzzleHttp\RequestOptions;
use PhpParser\Node;
use Rector\Transform\Rector\String_\StringToClassConstantRector;
use Rector\Transform\ValueObject\StringToClassConstant;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;

/**
 * @see \Guanguans\RectorRulesTests\Rector\Scalar\StringToClassConstantOfRequestOptionsRector\StringToClassConstantOfRequestOptionsRectorTest
 */
final class StringToClassConstantOfRequestOptionsRector extends AbstractProxyRector
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
                    namespace Guanguans\RectorRulesTests\Rector\Scalar\StringToClassConstantOfRequestOptionsRector\Fixture;

                    $allowRedirects = 'allow_redirects';
                    $auth = 'auth';
                    $body = 'body';
                    PHP,
                <<<'PHP'
                    /** @noinspection ALL */
                    namespace Guanguans\RectorRulesTests\Rector\Scalar\StringToClassConstantOfRequestOptionsRector\Fixture;

                    $allowRedirects = \GuzzleHttp\RequestOptions::ALLOW_REDIRECTS;
                    $auth = \GuzzleHttp\RequestOptions::AUTH;
                    $body = \GuzzleHttp\RequestOptions::BODY;
                    PHP,
            ),
        ];
    }

    /**
     * @param \PhpParser\Node\Scalar\String_ $node
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function rawRefactor(Node $node): ?Node
    {
        $this->makeProxyRector()->configure(array_map(
            static fn (
                string $value,
                string $name
            ): StringToClassConstant => new StringToClassConstant($value, RequestOptions::class, $name),
            $constants = array_filter((new \ReflectionClass(RequestOptions::class))->getConstants(), '\is_string'),
            array_keys($constants),
        ));

        return parent::rawRefactor($node);
    }

    protected function proxyRectorClass(): string
    {
        return StringToClassConstantRector::class;
    }
}
