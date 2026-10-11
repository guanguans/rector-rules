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
use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\Float_;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Scalar\String_;
use Rector\Transform\ValueObject\ScalarValueToConstFetch;
use Symfony\Component\HttpFoundation\Response;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;

/**
 * @see \Guanguans\RectorRulesTests\Rector\Scalar\ScalarValueToConstFetchOfResponseRector\ScalarValueToConstFetchOfResponseRectorTest
 */
final class ScalarValueToConstFetchOfResponseRector extends AbstractProxyRector
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
                    namespace Guanguans\RectorRulesTests\Rector\Scalar\ScalarValueToConstFetchOfResponseRector\Fixture;

                    $httpContinue = 100;
                    $httpSwitchingProtocols = 101;
                    $httpProcessing = 102;
                    PHP,
                <<<'PHP'
                    /** @noinspection ALL */
                    namespace Guanguans\RectorRulesTests\Rector\Scalar\ScalarValueToConstFetchOfResponseRector\Fixture;

                    $httpContinue = \Symfony\Component\HttpFoundation\Response::HTTP_CONTINUE;
                    $httpSwitchingProtocols = \Symfony\Component\HttpFoundation\Response::HTTP_SWITCHING_PROTOCOLS;
                    $httpProcessing = \Symfony\Component\HttpFoundation\Response::HTTP_PROCESSING;
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
            static fn (int $value, string $name): ScalarValueToConstFetch => new ScalarValueToConstFetch(
                // new (match (true) {
                //     \is_float($value) => Float_::class,
                //     \is_int($value) => Int_::class,
                //     \is_string($value) => String_::class,
                // })($value),
                // (static function (/* float|int|string */ $value): object {
                //     switch (true) {
                //         case \is_float($value):
                //             return new Float_($value);
                //         case \is_int($value):
                //             return new Int_($value);
                //         case \is_string($value):
                //         default:
                //             return new String_($value);
                //     }
                // })($value),
                new Int_($value),
                new ClassConstFetch(new FullyQualified(Response::class), new Identifier($name))
            ),
            $constants = array_filter(
                (new \ReflectionClass(Response::class))->getConstants(/* ReflectionClassConstant::IS_PUBLIC */),
                // static fn (mixed $value): bool => \is_float($value) || \is_int($value) || \is_string($value)
                '\is_int'
            ),
            array_keys($constants)
        ));

        return parent::rawRefactor($node);
    }

    protected function proxyRectorClass(): string
    {
        return ScalarValueToConstFetchRector::class;
    }
}
