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
use Illuminate\Support\Carbon as IlluminateCarbon;
use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\FuncCall\RenameFunctionRector;
use Rector\Renaming\Rector\Name\RenameClassRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__.'/../../config.php');
    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        Carbon::class => IlluminateCarbon::class,
    ]);
    $rectorConfig->ruleWithConfiguration(RenameFunctionRector::class, [
        'faker' => 'fake',
        'Pest\Faker\fake' => 'fake',
        'Pest\Faker\faker' => 'fake',
    ]);
};
