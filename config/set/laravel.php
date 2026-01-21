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

use Carbon\Carbon;
use Illuminate\Foundation\Application;
use Illuminate\Support\Carbon as IlluminateCarbon;
use Illuminate\Support\Str;
use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\FuncCall\RenameFunctionRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Transform\Rector\FuncCall\FuncCallToStaticCallRector;
use Rector\Transform\Rector\StaticCall\StaticCallToFuncCallRector;
use Rector\Transform\ValueObject\FuncCallToStaticCall;
use Rector\Transform\ValueObject\StaticCallToFuncCall;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__.'/../config.php');

    /**
     * @required laravel/framework
     */
    if (class_exists(Application::class)) {
        $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
            Carbon::class => IlluminateCarbon::class,
        ]);

        $rectorConfig->ruleWithConfiguration(RenameFunctionRector::class, [
            'Pest\Faker\fake' => 'fake',
            'Pest\Faker\faker' => 'fake',
            'faker' => 'fake',
        ]);
    }

    /**
     * @required illuminate/support
     */
    if (method_exists(Str::class, 'of')) {
        /**
         * @see https://github.com/laravel/framework/commit/d41e88519a6e4203b0986a40c5ac8670a157cf59
         */
        \function_exists('str')
            ? $rectorConfig->ruleWithConfiguration(StaticCallToFuncCallRector::class, [
                new StaticCallToFuncCall(Str::class, 'of', 'str'),
            ])
            : $rectorConfig->ruleWithConfiguration(FuncCallToStaticCallRector::class, [
                new FuncCallToStaticCall('str', Str::class, 'of'),
            ]);
    }
};
