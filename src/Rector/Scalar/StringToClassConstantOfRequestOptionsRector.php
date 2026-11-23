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
use Guanguans\RectorRules\Rector\String_\StringToClassConstantRector;
use Guanguans\RectorRules\Support\Utils;
use GuzzleHttp\RequestOptions;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Webmozart\Assert\Assert;

/**
 * @see \Guanguans\RectorRulesTests\Rector\Scalar\StringToClassConstantOfRequestOptionsRector\StringToClassConstantOfRequestOptionsRectorTest
 */
final class StringToClassConstantOfRequestOptionsRector extends AbstractProxyRector implements ConfigurableRectorInterface
{
    /** @var list<string> */
    private array $except = [
        'auth',
        'body',
        'cookies',
        'curl',
        'debug',
        'delay',
        'headers',
        'json',
        'progress',
        'query',
        'stream',
        'synchronous',
        'timeout',
        'version',
    ];

    public function configure(array $configuration): void
    {
        Assert::isList($configuration);
        Assert::allString($configuration);
        $this->except = $configuration;
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
                    namespace Guanguans\RectorRulesTests\Rector\Scalar\StringToClassConstantOfRequestOptionsRector\Fixture;

                    $allowRedirects = 'allow_redirects';
                    $auth = 'auth';
                    $body = 'body';
                    PHP,
                <<<'PHP'
                    /** @noinspection ALL */
                    namespace Guanguans\RectorRulesTests\Rector\Scalar\StringToClassConstantOfRequestOptionsRector\Fixture;

                    $allowRedirects = \GuzzleHttp\RequestOptions::ALLOW_REDIRECTS;
                    $auth = 'auth';
                    $body = \GuzzleHttp\RequestOptions::BODY;
                    PHP,
                ['auth', 'json', 'version']
            ),
        ];
    }

    protected function proxyRectorClass(): string
    {
        return StringToClassConstantRector::class;
    }

    /**
     * @throws \ErrorException
     * @throws \JsonException
     * @throws \ReflectionException
     */
    protected function configuration(): array
    {
        return Utils::configurationOfStringToClassConstantRector(
            RequestOptions::class,
            [RequestOptions::class => array_map('\strtoupper', $this->except)]
        );
    }
}
