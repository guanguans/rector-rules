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
use GuzzleHttp\RequestOptions;
use PhpParser\Node;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Transform\ValueObject\StringToClassConstant;
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
        'curl',
        'debug',
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
            $constants = array_filter(
                (new \ReflectionClass(RequestOptions::class))->getConstants(/* ReflectionClassConstant::IS_PUBLIC */),
                // '\is_string'
                fn ($value): bool => \is_string($value) && !\in_array($value, $this->except, true)
            ),
            array_keys($constants),
        ));

        return parent::rawRefactor($node);
    }

    protected function proxyRectorClass(): string
    {
        return StringToClassConstantRector::class;
    }
}
