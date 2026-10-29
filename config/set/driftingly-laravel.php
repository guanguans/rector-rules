<?php

/** @noinspection PhpInternalEntityUsedInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUndefinedNamespaceInspection */
declare(strict_types=1);

/**
 * Copyright (c) 2025-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/rector-rules
 */

use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Rector\Config\RectorConfig;
use Rector\Transform\NodeAnalyzer\FuncCallStaticCallToMethodCallAnalyzer;
use Rector\Transform\ValueObject\ArrayDimFetchToMethodCall;
use RectorLaravel\Rector\FuncCall\ArgumentFuncCallToMethodCallRector;
use RectorLaravel\Set\LaravelSetList;
use function Guanguans\RectorRules\Support\classes;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__.'/../config.php');

    /** @required https://packagist.org/packages/driftingly/rector-laravel */
    if (!class_exists(LaravelSetList::class) || !class_exists(Application::class)) {
        return;
    }

    $rectorConfig->sets(array_filter(
        (new ReflectionClass(LaravelSetList::class))->getConstants(/* ReflectionClassConstant::IS_PUBLIC */),
        static fn (string $name): bool => !preg_match('/^LARAVEL_\d{2,3}$/', $name) && !\in_array(
            $name,
            collect(['LARAVEL_STATIC_TO_INJECTION', 'LUMEN'])
                ->unless(
                    class_exists(ArrayDimFetchToMethodCall::class),
                    static fn (Collection $exceptSets): Collection => $exceptSets->push('LARAVEL_ARRAYACCESS_TO_METHOD_CALL')
                )
                ->all(),
            true
        ),
        \ARRAY_FILTER_USE_KEY
    ));

    $rectorConfig->rules(
        classes(static fn (string $class): bool => str_starts_with($class, 'RectorLaravel\Rector'))
            ->filter(static fn (ReflectionClass $reflectionClass): bool => $reflectionClass->isInstantiable())
            ->keys()
            ->unless(
                class_exists(FuncCallStaticCallToMethodCallAnalyzer::class),
                static fn (Collection $rules): Collection => $rules->reject(
                    static fn (string $rule): bool => ArgumentFuncCallToMethodCallRector::class === $rule
                )
            )
            ->all()
    );
};
