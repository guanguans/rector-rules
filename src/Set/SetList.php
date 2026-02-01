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

namespace Guanguans\RectorRules\Set;

/**
 * @api
 */
final class SetList
{
    public const ALL = __DIR__.'/../../config/set/all.php';
    public const COMMON = __DIR__.'/../../config/set/common.php';
    public const DRIFTINGLY_LARAVEL = __DIR__.'/../../config/set/driftingly-laravel.php';
    public const GUZZLE = __DIR__.'/../../config/set/guzzle.php';
    public const LARAVEL = __DIR__.'/../../config/set/laravel.php';
    public const PEST = __DIR__.'/../../config/set/pest.php';
    public const PHPBENCH = __DIR__.'/../../config/set/phpbench.php';
    public const PHPSTAN = __DIR__.'/../../config/set/phpstan.php';
    public const RECTOR = __DIR__.'/../../config/set/rector.php';
    public const SYMFONY = __DIR__.'/../../config/set/symfony.php';
}
