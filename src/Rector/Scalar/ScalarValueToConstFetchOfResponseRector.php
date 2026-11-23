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
use Guanguans\RectorRules\Support\Utils;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Webmozart\Assert\Assert;

/**
 * @see \Guanguans\RectorRulesTests\Rector\Scalar\ScalarValueToConstFetchOfResponseRector\ScalarValueToConstFetchOfResponseRectorTest
 */
final class ScalarValueToConstFetchOfResponseRector extends AbstractProxyRector implements ConfigurableRectorInterface
{
    /** @var list<string> */
    private array $except = [
        'HTTP_CONTINUE', // 100
        'HTTP_OK', // 200
        'HTTP_MULTIPLE_CHOICES', // 300
        'HTTP_BAD_REQUEST', // 400
        'HTTP_INTERNAL_SERVER_ERROR', // 500
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
                    namespace Guanguans\RectorRulesTests\Rector\Scalar\ScalarValueToConstFetchOfResponseRector\Fixture;

                    $httpContinue = 100;
                    $httpSwitchingProtocols = 101;
                    $httpProcessing = 102;
                    PHP,
                <<<'PHP'
                    /** @noinspection ALL */
                    namespace Guanguans\RectorRulesTests\Rector\Scalar\ScalarValueToConstFetchOfResponseRector\Fixture;

                    $httpContinue = 100;
                    $httpSwitchingProtocols = \Symfony\Component\HttpFoundation\Response::HTTP_SWITCHING_PROTOCOLS;
                    $httpProcessing = \Symfony\Component\HttpFoundation\Response::HTTP_PROCESSING;
                    PHP,
                ['HTTP_CONTINUE', 'HTTP_OK']
            ),
        ];
    }

    protected function proxyRectorClass(): string
    {
        return ScalarValueToConstFetchRector::class;
    }

    /**
     * @throws \ErrorException
     * @throws \JsonException
     * @throws \ReflectionException
     */
    protected function configuration(): array
    {
        return Utils::configurationOfScalarValueToConstFetchRector(
            Response::class,
            [Response::class => $this->except]
        );
    }
}
