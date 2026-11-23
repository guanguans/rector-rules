<?php

declare(strict_types=1);

/**
 * Copyright (c) 2025-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/rector-rules
 */

namespace Guanguans\RectorRules\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\Float_;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Scalar\String_;
use Rector\Transform\ValueObject\ScalarValueToConstFetch;
use Rector\Transform\ValueObject\StringToClassConstant;

/**
 * @api
 */
final class Utils
{
    /**
     * @see \PhpCsFixer\Hasher
     * @see \PhpCsFixer\Utils
     */
    private function __construct() {}

    /**
     * @param null|list<string>|string $classNameStartsWith
     * @param array<class-string|int, class-string|list<string>> $except
     *
     * @throws \ErrorException
     * @throws \JsonException
     * @throws \ReflectionException
     *
     * @return list<\Rector\Transform\ValueObject\StringToClassConstant>
     */
    public static function configurationOfStringToClassConstantRector($classNameStartsWith = null, array $except = []): array
    {
        return classes(static fn (string $class): bool => Str::startsWith($class, $classNameStartsWith ?? self::psrNamespaces()))
            ->tap(static function () use (&$refClassConstants, $except): void {
                $refClassConstants = collect($except)->flatMap(
                    /**
                     * @param array|class-string $constNamesOrClassName
                     * @param class-string|int $classNameOrIndex
                     *
                     * @throws \Exception
                     *
                     * @return list<\ReflectionClassConstant>
                     */
                    static fn ($constNamesOrClassName, $classNameOrIndex): array => \is_array($constNamesOrClassName)
                        ? array_map(
                            static fn (string $constName): \ReflectionClassConstant => new \ReflectionClassConstant(
                                $classNameOrIndex,
                                $constName
                            ),
                            $constNamesOrClassName
                        )
                        : (new \ReflectionClass($constNamesOrClassName))->getReflectionConstants()
                );
            })
            ->reduce(
                static fn (array $stringToClassConstants, \ReflectionClass $refClass): array => array_merge(
                    $stringToClassConstants,
                    array_map(
                        static fn (
                            string $value,
                            string $name
                        ): StringToClassConstant => new StringToClassConstant($value, $refClass->getName(), $name),
                        $constants = array_filter(
                            $refClass->getConstants(/* ReflectionClassConstant::IS_PUBLIC */),
                            static function (
                                $value,
                                string $name
                            ) use ($refClass, $refClassConstants): bool {
                                $refConstant = $refClass->getReflectionConstant($name);
                                \assert($refConstant instanceof \ReflectionClassConstant);
                                \assert($refClassConstants instanceof Collection);

                                return \is_string($value)
                                    && $refConstant->isPublic()
                                    && !$refClassConstants->containsStrict(
                                        static fn (
                                            \ReflectionClassConstant $refClassConstant
                                        ): bool => $refClass->getName() === $refClassConstant->class
                                            && $refClassConstant->getName() === $name
                                    );
                            },
                            \ARRAY_FILTER_USE_BOTH
                        ),
                        array_keys($constants),
                    ),
                ),
                [],
            );
    }

    /**
     * @param null|list<string>|string $classNameStartsWith
     * @param array<class-string|int, class-string|list<string>> $except
     *
     * @throws \ErrorException
     * @throws \JsonException
     * @throws \ReflectionException
     *
     * @return list<\Rector\Transform\ValueObject\ScalarValueToConstFetch>
     */
    public static function configurationOfScalarValueToConstFetchRector($classNameStartsWith = null, array $except = []): array
    {
        return classes(static fn (string $class): bool => Str::startsWith($class, $classNameStartsWith ?? self::psrNamespaces()))
            ->tap(static function () use (&$refClassConstants, $except): void {
                $refClassConstants = collect($except)->flatMap(
                    /**
                     * @param array|class-string $constNamesOrClassName
                     * @param class-string|int $classNameOrIndex
                     *
                     * @throws \Exception
                     *
                     * @return list<\ReflectionClassConstant>
                     */
                    static fn ($constNamesOrClassName, $classNameOrIndex): array => \is_array($constNamesOrClassName)
                        ? array_map(
                            static fn (string $constName): \ReflectionClassConstant => new \ReflectionClassConstant(
                                $classNameOrIndex,
                                $constName
                            ),
                            $constNamesOrClassName
                        )
                        : (new \ReflectionClass($constNamesOrClassName))->getReflectionConstants()
                );
            })
            ->reduce(
                static fn (array $scalarValueToConstFetches, \ReflectionClass $refClass): array => array_merge(
                    $scalarValueToConstFetches,
                    array_map(
                        static fn ($value, string $name): ScalarValueToConstFetch => new ScalarValueToConstFetch(
                            // new (match (true) {
                            //     \is_float($value) => Float_::class,
                            //     \is_int($value) => Int_::class,
                            //     \is_string($value) => String_::class,
                            // })($value),
                            (static function (/* float|int|string */ $value): object {
                                switch (true) {
                                    case \is_float($value):
                                        return new Float_($value);
                                    case \is_int($value):
                                        return new Int_($value);
                                    case \is_string($value):
                                    default:
                                        return new String_($value);
                                }
                            })($value),
                            new ClassConstFetch(new FullyQualified($refClass->getName()), new Identifier($name))
                        ),
                        $constants = array_filter(
                            $refClass->getConstants(/* ReflectionClassConstant::IS_PUBLIC */),
                            static function ($value, string $name) use ($refClass, $refClassConstants): bool {
                                $refConstant = $refClass->getReflectionConstant($name);
                                \assert($refConstant instanceof \ReflectionClassConstant);
                                \assert($refClassConstants instanceof Collection);

                                return (\is_float($value) || \is_int($value) || \is_string($value))
                                    && $refConstant->isPublic()
                                    && !$refClassConstants->containsStrict(
                                        static fn (
                                            \ReflectionClassConstant $refClassConstant
                                        ): bool => $refClass->getName() === $refClassConstant->class
                                            && $refClassConstant->getName() === $name
                                    );
                            },
                            \ARRAY_FILTER_USE_BOTH
                        ),
                        array_keys($constants)
                    )
                ),
                [],
            );
    }

    /**
     * @throws \JsonException
     *
     * @return list<string>
     *
     * @noinspection PhpSameParameterValueInspection
     */
    private static function psrNamespaces(?string $composerJsonFile = null): array
    {
        return array_keys(Arr::get(
            json_decode(
                file_get_contents($composerJsonFile ?? (getcwd().\DIRECTORY_SEPARATOR.'composer.json')),
                true,
                512,
                \JSON_THROW_ON_ERROR
            ),
            'autoload.psr-4',
            [],
        ));
    }
}
