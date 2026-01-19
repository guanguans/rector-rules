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

use PhpBench\Attributes\BeforeMethods;
use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\AnnotationToAttributeRector;
use Rector\Php80\ValueObject\AnnotationToAttribute;
use Rector\ValueObject\PhpVersion;

return static function (RectorConfig $rectorConfig): void {
    if (\PHP_VERSION_ID < PhpVersion::PHP_80 || !class_exists(BeforeMethods::class)) {
        return;
    }

    $rectorConfig->import(__DIR__.'/../config.php');
    $rectorConfig->ruleWithConfiguration(
        AnnotationToAttributeRector::class,
        array_reduce(
            glob(\sprintf('%s/*.php', \dirname((new ReflectionClass(BeforeMethods::class))->getFileName()))),
            static function (array $annotationToAttributes, string $file): array {
                $filename = pathinfo($file, \PATHINFO_FILENAME);

                if ('AbstractMethodsAttribute' === $filename) {
                    return $annotationToAttributes;
                }

                $annotationToAttributes[] = new AnnotationToAttribute($filename, "PhpBench\\Attributes\\$filename");

                return $annotationToAttributes;
            },
            []
        )
    );
};
