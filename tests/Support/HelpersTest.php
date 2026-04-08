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

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Rector\PhpParser\Parser\SimplePhpParser;
use function Guanguans\RectorRules\Support\classes;
use function Guanguans\RectorRules\Support\clone_node;
use function Guanguans\RectorRules\Support\is_class_of_all;
use function Guanguans\RectorRules\Support\is_class_of_any;
use function Guanguans\RectorRules\Support\is_instance_of_all;
use function Guanguans\RectorRules\Support\is_instance_of_any;
use function Guanguans\RectorRules\Support\is_subclass_of_all;
use function Guanguans\RectorRules\Support\is_subclass_of_any;

it('will throw `ReflectionException` when call classes function', function (): void {
    classes(fn (string $class): bool => \str($class)->startsWith('Illuminate\Support'));
})
    ->group(__DIR__, __FILE__)
    ->throws(ReflectionException::class);

it('can get classes', function (): void {
    expect(classes(fn (string $class): bool => \str($class)->startsWith(Str::class)))
        ->toBeInstanceOf(Collection::class);
})->group(__DIR__, __FILE__);

it('can deep clone node', function (): void {
    $node = (new SimplePhpParser)->parseFile(__FILE__)[0];

    expect(clone_node($node))
        ->toBeInstanceOf(\get_class($node))
        ->not->toBe($node)
        ->not->toEqual($node);
})->group(__DIR__, __FILE__);

it('is instance of any', function (): void {
    expect(is_instance_of_any(stdClass::class, [stdClass::class]))->toBeFalse();
    expect(is_instance_of_any(new stdClass, [stdClass::class]))->toBeTrue();
})->group(__DIR__, __FILE__);

it('is instance of all', function (): void {
    expect(is_instance_of_all(stdClass::class, [stdClass::class]))->toBeFalse();
    expect(is_instance_of_all(new stdClass, [stdClass::class]))->toBeTrue();
})->group(__DIR__, __FILE__);

it('is class of any', function (): void {
    expect(is_class_of_any(stdClass::class, [stdClass::class]))->toBeTrue();
    expect(is_class_of_any(stdClass::class, [stdClass::class], false))->toBeFalse();
    expect(is_class_of_any(new stdClass, [stdClass::class]))->toBeTrue();
})->group(__DIR__, __FILE__);

it('is class of all', function (): void {
    expect(is_class_of_all(stdClass::class, [stdClass::class]))->toBeTrue();
    expect(is_class_of_all(stdClass::class, [stdClass::class], false))->toBeFalse();
    expect(is_class_of_all(new stdClass, [stdClass::class]))->toBeTrue();
})->group(__DIR__, __FILE__);

it('is subclass of any', function (): void {
    expect(is_subclass_of_any(stdClass::class, [stdClass::class]))->toBeFalse();
    expect(is_subclass_of_any(stdClass::class, [stdClass::class], false))->toBeFalse();
    expect(is_subclass_of_any(new stdClass, [stdClass::class]))->toBeFalse();

    expect(is_subclass_of_any(Exception::class, [Throwable::class]))->toBeTrue();
    expect(is_subclass_of_any(Exception::class, [Throwable::class], false))->toBeFalse();
    expect(is_subclass_of_any(new Exception, [Throwable::class]))->toBeTrue();
})->group(__DIR__, __FILE__);

it('is subclass of all', function (): void {
    expect(is_subclass_of_all(stdClass::class, [stdClass::class]))->toBeFalse();
    expect(is_subclass_of_all(stdClass::class, [stdClass::class], false))->toBeFalse();
    expect(is_subclass_of_all(new stdClass, [stdClass::class]))->toBeFalse();

    expect(is_subclass_of_all(Exception::class, [Throwable::class]))->toBeTrue();
    expect(is_subclass_of_all(Exception::class, [Throwable::class], false))->toBeFalse();
    expect(is_subclass_of_all(new Exception, [Throwable::class]))->toBeTrue();
})->group(__DIR__, __FILE__);
