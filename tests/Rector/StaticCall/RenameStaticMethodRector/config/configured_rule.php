<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpFieldAssignmentTypeMismatchInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpVoidFunctionResultUsedInspection */
/** @noinspection SqlResolve */
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

use Guanguans\RectorRules\Rector\StaticCall\RenameStaticMethodRector;
use Guanguans\RectorRules\ValueObject\RenameStaticMethod;
use Guanguans\RectorRulesTests\Rector\StaticCall\RenameStaticMethodRector\Source\FormMacros;
use Nette\Utils\Html;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // Ensure that using the default configuration is valid.
    $rectorConfig->rule(RenameStaticMethodRector::class);
    $rectorConfig->ruleWithConfiguration(RenameStaticMethodRector::class, [
        new RenameStaticMethod(Html::class, 'add', Html::class, 'addHtml'),
        new RenameStaticMethod(
            FormMacros::class,
            'renderFormBegin',
            'Nette\Bridges\FormsLatte\Runtime',
            'renderFormBegin'
        ),
    ]);
};
