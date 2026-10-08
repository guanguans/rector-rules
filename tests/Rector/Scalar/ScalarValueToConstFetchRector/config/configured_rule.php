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

use Guanguans\RectorRules\Rector\Scalar\ScalarValueToConstFetchRector;
use Guanguans\RectorRulesTests\Rector\Scalar\ScalarValueToConstFetchRector\Source\ClassWithConst;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\Float_;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Scalar\String_;
use Rector\Config\RectorConfig;
use Rector\Transform\ValueObject\ScalarValueToConstFetch;

return static function (RectorConfig $rectorConfig): void {
    // Ensure that using the default configuration is valid.
    $rectorConfig->rule(ScalarValueToConstFetchRector::class);
    $rectorConfig->ruleWithConfiguration(ScalarValueToConstFetchRector::class, [
        new ScalarValueToConstFetch(
            new Int_(10),
            new ClassConstFetch(new FullyQualified(ClassWithConst::class), new Identifier('FOOBAR_INT'))
        ),
        new ScalarValueToConstFetch(
            new Float_(10.1),
            new ClassConstFetch(new FullyQualified(ClassWithConst::class), new Identifier('FOOBAR_FLOAT'))
        ),
        new ScalarValueToConstFetch(
            new String_('ABC'),
            new ClassConstFetch(new FullyQualified(ClassWithConst::class), new Identifier('FOOBAR_STRING'))
        ),
    ]);
};
