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

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Rector\Config\RectorConfig;
use Rector\Transform\Rector\String_\StringToClassConstantRector;
use Rector\Transform\ValueObject\StringToClassConstant;

return static function (RectorConfig $rectorConfig): void {
    if (!class_exists(Client::class)) {
        return;
    }

    $rectorConfig->import(__DIR__.'/../config.php');

    $rectorConfig->ruleWithConfiguration(StringToClassConstantRector::class, array_reduce(
        [
            RequestOptions::class,
        ],
        static fn (array $carry, string $class): array => array_merge(
            $carry,
            array_map(
                static fn (
                    string $string,
                    string $constant
                ): StringToClassConstant => new StringToClassConstant($string, $class, $constant),
                $constants = (new ReflectionClass($class))->getConstants(),
                array_keys($constants),
            ),
        ),
        [],
    ));
};
