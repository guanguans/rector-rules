<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpVoidFunctionResultUsedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection SqlResolve */
declare(strict_types=1);

/**
 * Copyright (c) 2025-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/rector-rules
 */

use Guanguans\RectorRules\Support\ComposerScripts;
use Symfony\Component\Console\Input\ArgvInput;

it('will throw `Error` when call private constructor', function (): void {
    expect(new ReflectionClass(ComposerScripts::class))->newInstanceWithoutConstructor()->toBeInstanceOf(ComposerScripts::class);
    new ComposerScripts;
})
    ->group(__DIR__, __FILE__)
    ->throws(
        Error::class,
        \sprintf('Call to private %s::__construct() from ', ComposerScripts::class)
    );

it('can make symfony style', function (): void {
    // ComposerScripts::dummyDebug('--debug');
    expect(ComposerScripts::makeSymfonyStyle())
        ->toBe(ComposerScripts::makeSymfonyStyle())
        ->not->toBe(ComposerScripts::makeSymfonyStyle(new ArgvInput(array_merge($_SERVER['argv'], ['--fake-option']))));
})->group(__DIR__, __FILE__);
