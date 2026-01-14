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

use Guanguans\RectorRules\Rector\File\AddNoinspectionDocblockToFileFirstStmtRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // Ensure that using the default configuration is valid.
    $rectorConfig->rule(AddNoinspectionDocblockToFileFirstStmtRector::class);
    $rectorConfig->ruleWithConfiguration(AddNoinspectionDocblockToFileFirstStmtRector::class, [
        '*/Fixture/fixture.php' => [
            'AnonymousFunctionStaticInspection',
            'StaticClosureCanBeUsedInspection',
        ],
        '*/fixture.php' => [
            'NullPointerExceptionInspection',
            'PhpPossiblePolymorphicInvocationInspection',
            'PhpUndefinedClassInspection',
            'PhpUnhandledExceptionInspection',
            'PhpVoidFunctionResultUsedInspection',
        ],
        '*/skip_same_inspections.php' => [
            'ALL',
        ],
    ]);
};
