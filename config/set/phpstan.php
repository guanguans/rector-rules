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

use Guanguans\RectorRules\Rector\Class_\UpdatePHPStanMethodNodeParamDocblockFromNodeTypesRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__.'/../config.php');
    $rectorConfig->rules([
        UpdatePHPStanMethodNodeParamDocblockFromNodeTypesRector::class,
    ]);
};
