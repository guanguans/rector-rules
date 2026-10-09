<?php

/** @noinspection PhpInternalEntityUsedInspection */
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
use Rector\Config\RectorConfig;
use RectorLaravel\Set\LaravelSetList;
use function Guanguans\RectorRules\Support\classes;

return static function (RectorConfig $rectorConfig): void {
    /** @required https://packagist.org/packages/driftingly/rector-laravel */
    if (!class_exists(LaravelSetList::class) || !class_exists(Application::class)) {
        return;
    }

    $rectorConfig->import(__DIR__.'/../config.php');

    $rectorConfig->sets(array_filter(
        (new ReflectionClass(LaravelSetList::class))->getConstants(),
        static fn (string $name): bool => !preg_match('/^LARAVEL_\d{2,3}$/', $name) && !\in_array(
            $name,
            ['LARAVEL_STATIC_TO_INJECTION', 'LUMEN'],
            true
        ),
        \ARRAY_FILTER_USE_KEY
    ));

    $rectorConfig->rules(
        classes(static fn (string $class): bool => str_starts_with($class, 'RectorLaravel\Rector'))
            ->filter(static fn (ReflectionClass $reflectionClass): bool => $reflectionClass->isInstantiable())
            ->keys()
            // ->dd()
            ->all()
    );
};
