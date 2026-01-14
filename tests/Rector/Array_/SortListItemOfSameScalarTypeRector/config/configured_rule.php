<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpVoidFunctionResultUsedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
declare(strict_types=1);

/**
 * Copyright (c) 2025-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/rector-rules
 */

use Guanguans\RectorRules\Rector\Array_\SortListItemOfSameScalarTypeRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // Ensure that using the default configuration is valid.
    $rectorConfig->rule(SortListItemOfSameScalarTypeRector::class);
    $rectorConfig->ruleWithConfiguration(SortListItemOfSameScalarTypeRector::class, [
        'ignore_comment' => false,
        'ignore_docblock' => false,
        // 'sort_comparator' => static fn (string $a, string $b): int => $a <=> $b,
        // 'sort_comparator' => 'strcasecmp',
        // 'sort_comparator' => 'strcmp',
        // 'sort_comparator' => 'strnatcasecmp',
        'sort_comparator' => 'strnatcmp',
        // 'sort_direction' => 'desc',
        'sort_direction' => 'asc',
    ]);
};
