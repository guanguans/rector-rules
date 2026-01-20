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
    public const LARAVEL_80 = __DIR__.'/../../config/set/laravel/laravel-80.php';
    public const LARAVEL_90 = __DIR__.'/../../config/set/laravel/laravel-90.php';
    public const LARAVEL_COMMON = __DIR__.'/../../config/set/laravel/laravel-common.php';
    public const PHPBENCH = __DIR__.'/../../config/set/phpbench.php';
    public const PHPSTAN = __DIR__.'/../../config/set/phpstan.php';
    public const RECTOR = __DIR__.'/../../config/set/rector.php';
}
