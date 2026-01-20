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

use Illuminate\Support\Str;
use Rector\Config\RectorConfig;
use Rector\Transform\Rector\FuncCall\FuncCallToStaticCallRector;
use Rector\Transform\ValueObject\FuncCallToStaticCall;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__.'/../../config.php');

    /** @see https://github.com/laravel/framework/commit/d41e88519a6e4203b0986a40c5ac8670a157cf59 */
    $rectorConfig->ruleWithConfiguration(FuncCallToStaticCallRector::class, [
        new FuncCallToStaticCall('str', Str::class, 'of'),
    ]);
};
