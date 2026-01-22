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

use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\Int_;
use Rector\Config\RectorConfig;
use Rector\Transform\Rector\Scalar\ScalarValueToConstFetchRector;
use Rector\Transform\ValueObject\ScalarValueToConstFetch;
use Symfony\Component\HttpFoundation\Response;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__.'/../config.php');

    /**
     * @required symfony/http-foundation
     */
    if (class_exists(Response::class)) {
        $rectorConfig->ruleWithConfiguration(ScalarValueToConstFetchRector::class, array_map(
            static fn (int $value, string $constant): ScalarValueToConstFetch => new ScalarValueToConstFetch(
                new Int_($value),
                new ClassConstFetch(new FullyQualified(Response::class), new Identifier($constant))
            ),
            $constants = array_filter(
                (new ReflectionClass(Response::class))->getConstants(),
                static fn ($value): bool => \is_int($value),
            ),
            array_keys($constants)
        ));
    }
};
