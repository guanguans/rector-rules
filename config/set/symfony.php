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

use Guanguans\RectorRules\Rector\Scalar\ScalarValueToConstFetchOfResponseRector;
use Rector\Config\RectorConfig;
use Symfony\Component\HttpFoundation\Response;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__.'/../config.php');

    /**
     * @required https://packagist.org/packages/symfony/http-foundation
     */
    if (class_exists(Response::class)) {
        $rectorConfig->rules([
            ScalarValueToConstFetchOfResponseRector::class,
        ]);
    }
};
