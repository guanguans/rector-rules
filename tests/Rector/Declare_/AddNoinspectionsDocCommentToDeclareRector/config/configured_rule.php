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

use Guanguans\RectorRules\Rector\Declare_\AddNoinspectionsDocCommentToDeclareRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(AddNoinspectionsDocCommentToDeclareRector::class, [
        '*/Fixture/fixture.php' => [
            'StaticClosureCanBeUsedInspection',
            'PhpPossiblePolymorphicInvocationInspection',
            'PhpUnhandledExceptionInspection',
        ],

        '*/fixture.php' => [
            'NullPointerExceptionInspection',
            'AnonymousFunctionStaticInspection',
        ],
    ]);
};
