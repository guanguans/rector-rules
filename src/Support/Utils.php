<?php

/** @noinspection D */
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

use Guanguans\RectorRules\ValueObject\ScalarValueToConstFetch;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\Float_;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Scalar\String_;
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
     * @param null|list<string>|string $namespacesOrClasses
     * @param array<class-string|int, list<string>|string> $skip
     *
     * @throws \ErrorException
     * @throws \JsonException
     * @throws \ReflectionException
     *
     * @return list<\Rector\Transform\ValueObject\StringToClassConstant>
     */
    public static function configurationOfStringToClassConstantRector($namespacesOrClasses = null, array $skip = []): array
    {
        return self::filteredRefClassConstants($namespacesOrClasses, $skip)
            ->filter(static fn (\ReflectionClassConstant $refClassConstant): bool => \is_string($refClassConstant->getValue()))
            ->map(static fn (\ReflectionClassConstant $refClassConstant): StringToClassConstant => new StringToClassConstant(
                $refClassConstant->getValue(),
                $refClassConstant->class,
                $refClassConstant->getName()
            ))
            // ->dd()
            ->values()
            ->all();
    }

    /**
     * @param null|list<string>|string $namespacesOrClasses
     * @param array<class-string|int, list<string>|string> $skip
     *
     * @throws \ErrorException
     * @throws \JsonException
     * @throws \ReflectionException
     *
     * @return list<\Guanguans\RectorRules\ValueObject\ScalarValueToConstFetch>
     */
    public static function configurationOfScalarValueToConstFetchRector($namespacesOrClasses = null, array $skip = []): array
    {
        return self::filteredRefClassConstants($namespacesOrClasses, $skip)
            ->filter(static fn (\ReflectionClassConstant $refClassConstant): bool => (
                \is_float($refClassConstant->getValue())
                || \is_int($refClassConstant->getValue())
                || \is_string($refClassConstant->getValue())
            ))
            ->map(static fn (\ReflectionClassConstant $refClassConstant): ScalarValueToConstFetch => new ScalarValueToConstFetch(
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
                })($refClassConstant->getValue()),
                new ClassConstFetch(new FullyQualified($refClassConstant->class), new Identifier($refClassConstant->getName()))
            ))
            // ->dd()
            ->values()
            ->all();
    }

    /**
     * @param null|list<string>|string $namespacesOrClasses
     * @param array<class-string|int, list<string>|string> $skip
     *
     * @throws \ErrorException
     * @throws \JsonException
     * @throws \ReflectionException
     *
     * @noinspection TransitiveDependenciesUsageInspection
     */
    private static function filteredRefClassConstants($namespacesOrClasses = null, array $skip = []): Collection
    {
        $skipGroup = collect($skip)
            ->flatMap(
                static fn ($namespaceOrClassOrConsts, $classOrIndex): array => \is_array($namespaceOrClassOrConsts)
                    ? array_map(
                        static fn (string $const): \ReflectionClassConstant => new \ReflectionClassConstant($classOrIndex, $const),
                        $namespaceOrClassOrConsts
                    )
                    : (
                        class_exists($namespaceOrClassOrConsts)
                            ? (new \ReflectionClass($namespaceOrClassOrConsts))->getReflectionConstants(
                                /* ReflectionClassConstant::IS_PUBLIC */
                            )
                            : [$namespaceOrClassOrConsts]
                    )
            )
            ->groupBy(static fn ($namespaceOrClassOrRefClassConstant): string => (
                $namespaceOrClassOrRefClassConstant instanceof \ReflectionClassConstant ? 'refClassConstants' : 'namespaces'
            ));
        $skipNamespaces = $skipGroup->get('namespaces', collect());
        $skipRefClassConstants = $skipGroup->get('refClassConstants', collect());

        return collect($namespacesOrClasses ?? array_keys(Arr::get(
            json_decode(file_get_contents(getcwd().\DIRECTORY_SEPARATOR.'composer.json'), true, 512, \JSON_THROW_ON_ERROR),
            'autoload.psr-4',
            [],
        )))
            ->groupBy(static fn (string $namespaceOrClass): string => class_exists($namespaceOrClass) ? 'classes' : 'namespaces')
            ->flatMap(
                static fn (Collection $namespacesOrClasses, string $type): Collection => 'classes' === $type
                    ? $namespacesOrClasses->mapWithKeys(static fn (string $class): array => [$class => new \ReflectionClass($class)])
                    : classes(static fn (string $class): bool => (
                        Str::startsWith($class, $namespacesOrClasses->all()) && !Str::startsWith($class, $skipNamespaces->all())
                    ))
            )
            // ->keys()->dd()
            ->flatMap(static fn (\ReflectionClass $refClass): array => $refClass->getReflectionConstants(
                /* ReflectionClassConstant::IS_PUBLIC */
            ))
            ->filter(
                static fn (\ReflectionClassConstant $refClassConstant): bool => $refClassConstant->isPublic()
                    && !$skipRefClassConstants->contains(static fn (\ReflectionClassConstant $skipRefClassConstant): bool => (
                        $skipRefClassConstant->class === $refClassConstant->class
                        && $skipRefClassConstant->getName() === $refClassConstant->getName()
                    ))
            );
    }
}
