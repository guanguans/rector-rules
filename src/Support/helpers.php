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

namespace Guanguans\RectorRules\Support;

use Composer\Autoload\ClassLoader;
use Illuminate\Support\Collection;

if (!\function_exists('Guanguans\RectorRules\Support\classes')) {
    /**
     * @see https://github.com/illuminate/collections
     * @see https://github.com/alekitto/class-finder
     * @see https://github.com/ergebnis/classy
     * @see https://gitlab.com/hpierce1102/ClassFinder
     * @see https://packagist.org/packages/haydenpierce/class-finder
     * @see \get_declared_classes()
     * @see \get_declared_interfaces()
     * @see \get_declared_traits()
     * @see \DG\BypassFinals::enable()
     * @see \Composer\Util\ErrorHandler
     * @see \Monolog\ErrorHandler
     * @see \PhpCsFixer\ExecutorWithoutErrorHandler
     * @see \Phrity\Util\ErrorHandler
     *
     * @internal
     *
     * @param null|(callable(class-string, string): bool) $filter
     *
     * @return \Illuminate\Support\Collection<class-string, \ReflectionClass>
     *
     * @noinspection PhpUndefinedNamespaceInspection
     */
    function classes(?callable $filter = null): Collection
    {
        static $classes;
        $classes ??= collect(spl_autoload_functions())->flatMap(
            static fn (callable $loader): array => \is_array($loader) && $loader[0] instanceof ClassLoader
                ? $loader[0]->getClassMap()
                : []
        );

        return $classes
            ->when(
                \is_callable($filter),
                static fn (Collection $classes): Collection => $classes->filter(
                    static fn (string $file, string $class) => $filter($class, $file)
                )
            )
            ->mapWithKeys(static function (string $file, string $class): array {
                try {
                    return [$class => new \ReflectionClass($class)];
                } catch (\Throwable $throwable) {
                    return [$class => $throwable];
                }
            });
    }
}

if (!\function_exists('Guanguans\RectorRules\Support\is_instance_of_any')) {
    /**
     * @see array_all()
     * @see array_any()
     * @see \Webmozart\Assert\Assert::allIsInstanceOf()
     * @see \Webmozart\Assert\Assert::allIsInstanceOfAny()
     * @see \Webmozart\Assert\Assert::isInstanceOf()
     * @see \Webmozart\Assert\Assert::isInstanceOfAny()
     *
     * @param mixed $objectOrClass
     * @param list<class-string> $classes
     */
    function is_instance_of_any($objectOrClass, array $classes): bool
    {
        foreach ($classes as $class) {
            if ($objectOrClass instanceof $class) {
                return true;
            }
        }

        return false;
    }
}

if (!\function_exists('Guanguans\RectorRules\Support\is_instance_of_all')) {
    /**
     * @param mixed $objectOrClass
     * @param list<class-string> $classes
     */
    function is_instance_of_all($objectOrClass, array $classes): bool
    {
        foreach ($classes as $class) {
            if (!$objectOrClass instanceof $class) {
                return false;
            }
        }

        return true;
    }
}

if (!\function_exists('Guanguans\RectorRules\Support\is_class_of_any')) {
    /**
     * @see is_a()
     * @see \Webmozart\Assert\Assert::isAnyOf()
     * @see \Webmozart\Assert\Assert::isAOf()
     *
     * @param mixed $objectOrClass
     * @param list<class-string> $classes
     *
     * @noinspection CallableParameterUseCaseInTypeContextInspection
     */
    function is_class_of_any($objectOrClass, array $classes, ?bool $allowString = null): bool
    {
        $allowString ??= \is_string($objectOrClass);

        foreach ($classes as $class) {
            if (is_a($objectOrClass, $class, $allowString)) {
                return true;
            }
        }

        return false;
    }
}

if (!\function_exists('Guanguans\RectorRules\Support\is_class_of_all')) {
    /**
     * @param mixed $objectOrClass
     * @param list<class-string> $classes
     *
     * @noinspection CallableParameterUseCaseInTypeContextInspection
     */
    function is_class_of_all($objectOrClass, array $classes, ?bool $allowString = null): bool
    {
        $allowString ??= \is_string($objectOrClass);

        foreach ($classes as $class) {
            if (!is_a($objectOrClass, $class, $allowString)) {
                return false;
            }
        }

        return true;
    }
}

if (!\function_exists('Guanguans\RectorRules\Support\is_subclass_of_any')) {
    /**
     * @see is_subclass_of()
     * @see \Webmozart\Assert\Assert::subclassOf()
     *
     * @param mixed $objectOrClass
     * @param list<class-string> $classes
     */
    function is_subclass_of_any($objectOrClass, array $classes, bool $allowString = true): bool
    {
        foreach ($classes as $class) {
            if (is_subclass_of($objectOrClass, $class, $allowString)) {
                return true;
            }
        }

        return false;
    }
}

if (!\function_exists('Guanguans\RectorRules\Support\is_subclass_of_all')) {
    /**
     * @param mixed $objectOrClass
     * @param list<class-string> $classes
     */
    function is_subclass_of_all($objectOrClass, array $classes, bool $allowString = true): bool
    {
        foreach ($classes as $class) {
            if (!is_subclass_of($objectOrClass, $class, $allowString)) {
                return false;
            }
        }

        return true;
    }
}
