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

namespace Guanguans\RectorRulesTests\Rector\Name\RenameToPsrNameRector;

use Guanguans\RectorRulesTests\Rector\AbstractRectorTestCase;

final class RenameToPsrNameRectorTest extends AbstractRectorTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SERVER['argv'][] = '--debug';
    }

    protected static function directory(): string
    {
        return __DIR__;
    }
}
