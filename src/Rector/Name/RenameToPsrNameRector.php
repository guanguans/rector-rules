<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */
/** @noinspection EfferentObjectCouplingInspection */
/** @noinspection PhpUnusedAliasInspection */
/** @noinspection PropertyCanBeStaticInspection */
declare(strict_types=1);

/**
 * Copyright (c) 2025-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/rector-rules
 */

namespace Guanguans\RectorRules\Rector\Name;

use Guanguans\RectorRules\Exception\RectorErrorException;
use Guanguans\RectorRules\Rector\AbstractRector;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PhpParser\Error;
use PhpParser\ErrorHandler\Collecting;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\Const_;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\PropertyItem;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\EnumCase;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\UseItem;
use PHPStan\Analyser\Scope;
use Rector\Console\Style\SymfonyStyleFactory;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\PHPStan\ScopeFetcher;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Webmozart\Assert\Assert;
use function Guanguans\RectorRules\Support\is_instance_of_any;

/**
 * @see \Guanguans\RectorRulesTests\Rector\Name\RenameToPsrNameRector\RenameToPsrNameRectorTest
 */
final class RenameToPsrNameRector extends AbstractRector implements ConfigurableRectorInterface
{
    /** @var list<string> */
    private array $except = [
        '*_',
        '_*',

        // 'class',
        'false',
        'null',
        'parent',
        'self',
        'static',
        'stdClass',
        'true',

        /** @see https://www.php.net/manual/zh/reserved.variables.php */
        '_COOKIE',
        '_ENV',
        '_FILES',
        '_GET',
        '_POST',
        '_REQUEST',
        '_SERVER',
        '_SESSION',
        'GLOBALS',
        'HTTP_ENV_VARS',
        'HTTP_GET_VARS',
        'HTTP_POST_FILES',
        'HTTP_POST_VARS',
        'HTTP_RAW_POST_DATA',
        'http_response_header',
        'HTTP_SERVER_VARS',
        'HTTP_SESSION_VARS',
        'php_errormsg',

        /** @see https://www.php.net/streamwrapper */
        'dir_closedir',
        'dir_opendir',
        'dir_readdir',
        'dir_rewinddir',
        'stream_cast',
        'stream_close',
        'stream_eof',
        'stream_flush',
        'stream_lock',
        'stream_metadata',
        'stream_open',
        'stream_read',
        'stream_seek',
        'stream_set_option',
        'stream_stat',
        'stream_tell',
        'stream_truncate',
        'stream_write',
        'unlink',
        'url_stat',

        /** @see https://www.php.net/manual/zh/class.php-user-filter.php */
        'php_user_filter',
    ];
    private Collecting $collecting;

    public function __construct(Collecting $collecting, SymfonyStyleFactory $symfonyStyleFactory)
    {
        $this->collecting = $collecting;

        register_shutdown_function(
            /**
             * @throws \ReflectionException
             */
            function (SymfonyStyleFactory $symfonyStyleFactory): void { // @codeCoverageIgnoreStart
                $rectorStyle = $symfonyStyleFactory->create();

                $reflectionProperty = (new \ReflectionObject($rectorStyle))->getParentClass()->getProperty('input');
                \PHP_VERSION_ID < 80100 and $reflectionProperty->setAccessible(true);
                $input = $reflectionProperty->getValue($rectorStyle);

                if (
                    $this->collecting->hasErrors()
                    && $rectorStyle->isDebug()
                    && 'console' === $input->getParameterOption('--output-format', 'console', true)
                ) {
                    $rectorStyle->warning(
                        collect($this->collecting->getErrors())
                            ->map(static fn (Error $error): string => $error->getRawMessage())
                            // ->map(static fn (Error $error): string => $error->getMessage())
                            // ->map(static fn (Error $error): string => $error->getMessageWithColumnInfo())
                            ->unique()
                            ->all()
                    );
                }
            }, // @codeCoverageIgnoreEnd
            $symfonyStyleFactory
        );
    }

    public function getNodeTypes(): array
    {
        return [
            FuncCall::class,
            Identifier::class,
            Name::class,
            Variable::class,
        ];
    }

    /**
     * @see https://github.com/jawira/case-converter
     *
     * @param \PhpParser\Node\Expr\FuncCall|\PhpParser\Node\Expr\Variable|\PhpParser\Node\Identifier|\PhpParser\Node\Name $node
     *
     * @noinspection BadExceptionsProcessingInspection
     */
    public function refactor(Node $node): ?Node
    {
        try {
            // dump($node->getAttribute('parent'));
            // ScopeFetcher::fetch($node);
            if ($this->shouldLowerSnakeName($node)) {
                return $this->rename($node, static fn (string $name): string => (string) Str::of($name)->snake()->lower());
            }

            if ($this->shouldUcfirstCamelName($node)) {
                return $this->rename($node, static fn (string $name): string => (string) Str::of($name)->camel()->ucfirst());
            }

            if ($this->shouldUpperSnakeName($node)) {
                return $this->rename($node, static fn (string $name): string => (string) Str::of($name)->snake()->upper());
            }

            if ($this->shouldLcfirstCamelName($node)) {
                // return $this->rename($node, static fn (string $name): string => (string) Str::of($name)->camel()->lcfirst());
                return $this->rename($node, static fn (string $name): string => (string) Str::of($name)->camel()->pipe('lcfirst'));
            }
        } catch (Error $error) {
            $this->collecting->handleError($error);
        }

        return null;
    }

    /**
     * @param list<class-string> $configuration
     */
    public function configure(array $configuration): void
    {
        Assert::allStringNotEmpty($configuration);
        $this->except = array_merge($this->except, $configuration);
    }

    /**
     * @see https://github.com/barryvdh/laravel-ide-helper/blob/master/resources/views/meta.php
     *
     * @throws \Symplify\RuleDocGenerator\Exception\ShouldNotHappenException
     *
     * @return list<\Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample>
     */
    protected function codeSamples(): array
    {
        return [
            new ConfiguredCodeSample(
                <<<'PHP'
                    /** @noinspection ALL */
                    // @formatter:off
                    // phpcs:ignoreFile

                    // lower snake
                    use function functionName;
                    function functionName(){}
                    functionName();
                    call_user_func('functionName');
                    call_user_func_array('functionName', []);
                    function_exists('functionName');

                    // ucfirst camel
                    // #[attribute_name()]
                    class class_name{}
                    // enum enum_name{}
                    // enum Enum{case case_name;}
                    interface interface_name{}
                    trait trait_name{}
                    class Foo extends class_name implements interface_name{}
                    class_name::$property;
                    class_name::CONST;
                    class_name::method();
                    // enum Enum implements interface_name{}
                    use class_name;
                    use trait_name;
                    class_alias('class_name', 'alias_class_name');
                    class_alias($className, 'alias_class_name');
                    class_exists('class_name');
                    class_implements('class_name');
                    class_parents('class_name');
                    class_uses('class_name');
                    enum_exists('enum_name');
                    get_class_methods('class_name');
                    get_class_vars('class_name');
                    get_parent_class('class_name');
                    interface_exists('interface_name');
                    is_subclass_of('class_name', 'parent_class_name');
                    is_subclass_of($className, 'parent_class_name');
                    trait_exists('trait_name', true);

                    // upper snake
                    use const constName;
                    class Foo{public const constName = 'const';}
                    Foo::constName;
                    define('constName', 'const');
                    defined('constName');
                    constant('constName');
                    constant('Foo::constName');
                    constName;

                    // lcfirst camel
                    $var_name;
                    $object->method_name();
                    $object->property_name;
                    call_user_method('method_name', $object);
                    call_user_method_array('method_name', $object);
                    class Foo{public $property_name;}
                    class Foo{public function method_name(){}}
                    class Foo{public int $property_name;}
                    Foo::$property_name;
                    Foo::method_name();
                    method_exists($object, 'method_name');
                    property_exists($object, 'property_name');
                    PHP,
                <<<'PHP'
                    /** @noinspection ALL */
                    // @formatter:off
                    // phpcs:ignoreFile

                    // lower snake
                    use function function_name;
                    function function_name(){}
                    \function_name();
                    call_user_func('function_name');
                    call_user_func_array('function_name', []);
                    function_exists('function_name');

                    // ucfirst camel
                    // #[attribute_name()]
                    class ClassName{}
                    // enum enum_name{}
                    // enum Enum{case case_name;}
                    interface InterfaceName{}
                    trait TraitName{}
                    class Foo extends \ClassName implements \InterfaceName{}
                    \ClassName::$property;
                    \ClassName::CONST;
                    \ClassName::method();
                    // enum Enum implements interface_name{}
                    use ClassName;
                    use TraitName;
                    class_alias('ClassName', 'AliasClassName');
                    class_alias($className, 'AliasClassName');
                    class_exists('ClassName');
                    class_implements('ClassName');
                    class_parents('ClassName');
                    class_uses('ClassName');
                    enum_exists('EnumName');
                    get_class_methods('ClassName');
                    get_class_vars('ClassName');
                    get_parent_class('ClassName');
                    interface_exists('InterfaceName');
                    is_subclass_of('ClassName', 'ParentClassName');
                    is_subclass_of($className, 'ParentClassName');
                    trait_exists('TraitName', true);

                    // upper snake
                    use const CONST_NAME;
                    class Foo{public const CONST_NAME = 'const';}
                    Foo::CONST_NAME;
                    define('CONST_NAME', 'const');
                    defined('CONST_NAME');
                    constant('CONST_NAME');
                    constant('FOO::CONST_NAME');
                    \CONST_NAME;

                    // lcfirst camel
                    $varName;
                    $object->methodName();
                    $object->propertyName;
                    call_user_method('methodName', $object);
                    call_user_method_array('methodName', $object);
                    class Foo{public $propertyName;}
                    class Foo{public function methodName(){}}
                    class Foo{public int $propertyName;}
                    Foo::$propertyName;
                    Foo::methodName();
                    method_exists($object, 'methodName');
                    property_exists($object, 'propertyName');
                    PHP,
                ['afterAll', 'afterEach', 'assertMatches*Snapshot', 'beforeAll', 'beforeEach', 'PDO']
            ),
        ];
    }

    /**
     * @see \Rector\NodeNameResolver\NodeNameResolver
     * @see \Rector\Renaming\Collector\RenamedNameCollector
     *
     * @param \PhpParser\Node\Expr\FuncCall|\PhpParser\Node\Expr\Variable|\PhpParser\Node\Identifier|\PhpParser\Node\Name $node
     */
    private function rename(Node $node, callable $renamer): ?Node
    {
        $renamer = $this->wrapRenamer($renamer, $node);

        if ($node instanceof Name) {
            if (($newLastName = $renamer($lastName = $node->getLast())) === $lastName) {
                return null;
            }

            $newNameNode = Name::concat($node->slice(0, -1), $newLastName);
            \assert($newNameNode instanceof Name);
            $node->name = $newNameNode->name;

            return $node;
        }

        if (
            is_instance_of_any($node, [
                Variable::class,
                Identifier::class,
            ])
        ) {
            if (($newName = $renamer($node->name)) === $node->name) {
                return null;
            }

            /**
             * In the `\PhpParser\Node\FunctionLike` parameter,
             * the `\PhpParser\Node\Expr\Variable` node does not have a scope attribute,
             * and even if it is renamed, `rector` will report an error.
             *
             * "System error: "Node "PhpParser\Node\Expr\Variable" with is missing scope required for scope refresh"
             * ```
             * function func_name($var_name): void {}
             * ```.
             */
            if ($node instanceof Variable && !$node->getAttribute(AttributeKey::SCOPE) instanceof Scope) {
                throw new RectorErrorException(
                    $this,
                    "The variable name [$node->name] cannot be renamed to [$newName] because of missing scope.",
                    $node->getAttributes()
                );
            }

            $node->name = $newName;

            return $node;
        }

        $hasChanged = false;

        if ($node instanceof FuncCall) {
            if (
                $this->isNames($node, [
                    'call_user_func',
                    'call_user_func_array',
                    'call_user_method',
                    'call_user_method_array',
                    'class_alias',
                    'class_exists',
                    'class_implements',
                    'class_parents',
                    'class_uses',
                    'constant',
                    'define',
                    'defined',
                    'enum_exists',
                    'function_exists',
                    'get_class_methods',
                    'get_class_vars',
                    'get_parent_class',
                    'interface_exists',
                    'is_subclass_of',
                    'trait_exists',
                ])
                && $this->hasFuncCallIndexStringArg($node, 0)
            ) {
                $this->renameFuncCallIndexStringArg($node->args[0], $renamer) and $hasChanged = true;
            }

            if (
                $this->isNames($node, [
                    'class_alias',
                    'is_subclass_of',
                    'method_exists',
                    'property_exists',
                ])
                && $this->hasFuncCallIndexStringArg($node, 1)
            ) {
                $this->renameFuncCallIndexStringArg($node->args[1], $renamer) and $hasChanged = true;
            }
        }

        return $hasChanged ? $node : null;
    }

    /**
     * @param \PhpParser\Node\Expr\FuncCall|\PhpParser\Node\Expr\Variable|\PhpParser\Node\Identifier|\PhpParser\Node\Name $node
     */
    private function shouldLowerSnakeName(Node $node): bool
    {
        $parent = $node->getAttribute('parent');
        $grandparent = $parent ? $parent->getAttribute('parent') : null;

        // function function_name(){}
        if ($node instanceof Identifier && $parent instanceof Function_) {
            return true;
        }

        // function_name();
        // use function function_name;
        if (
            $node instanceof Name
            && (
                $parent instanceof FuncCall
                || (
                    $parent instanceof UseItem
                    && $grandparent instanceof Use_
                    && (
                        (Use_::TYPE_UNKNOWN === $grandparent->type && Use_::TYPE_FUNCTION === $parent->type)
                        || (Use_::TYPE_FUNCTION === $grandparent->type && Use_::TYPE_UNKNOWN === $parent->type)
                    )
                )
            )
        ) {
            return true;
        }

        return $node instanceof FuncCall
            && $this->isNames($node, [
                // call_user_func('function_name');
                'call_user_func',
                // call_user_func_array('function_name');
                'call_user_func_array',
                // function_exists('function_name');
                'function_exists',
            ])
            && $this->hasFuncCallIndexStringArg($node, 0);
    }

    /**
     * @param \PhpParser\Node\Expr\FuncCall|\PhpParser\Node\Expr\Variable|\PhpParser\Node\Identifier|\PhpParser\Node\Name $node
     */
    private function shouldUcfirstCamelName(Node $node): bool
    {
        $parent = $node->getAttribute('parent');
        $grandparent = $parent ? $parent->getAttribute('parent') : null;

        if (
            $node instanceof Identifier
            && is_instance_of_any($parent, [
                // interface InterfaceName{}
                Interface_::class,
                // class ClassName{}
                Class_::class,
                // trait TraitName{}
                Trait_::class,
                // enum EnumName{}
                Enum_::class,
                // enum Enum{case CaseName;}
                EnumCase::class,
            ])
        ) {
            return true;
        }

        if (
            $node instanceof Name && (
                // use ClassName;
                (
                    $parent instanceof UseItem
                    && $grandparent instanceof Use_
                    && (
                        (Use_::TYPE_UNKNOWN === $grandparent->type && Use_::TYPE_NORMAL === $parent->type)
                        || (Use_::TYPE_NORMAL === $grandparent->type && Use_::TYPE_UNKNOWN === $parent->type)
                    )
                )
                || is_instance_of_any($parent, [
                    // #[\AttributeName]
                    Attribute::class,
                    // class Foo extends ClassName implements InterfaceName{}
                    Class_::class,
                    // enum Enum implements InterfaceName{}
                    Enum_::class,
                    // use TraitName;
                    TraitUse::class,
                    // ClassName::CONST;
                    ClassConstFetch::class,
                    // ClassName::$property;
                    StaticPropertyFetch::class,
                    // ClassName::method();
                    StaticCall::class,
                ])
            )
        ) {
            return true;
        }

        if ($node instanceof FuncCall) {
            if (
                $this->isNames($node, [
                    // class_alias('ClassName', 'AliasClassName');
                    'class_alias',
                    // class_exists('ClassName');
                    'class_exists',
                    // class_implements('ClassName');
                    'class_implements',
                    // class_parents('ClassName');
                    'class_parents',
                    // class_uses('ClassName');
                    'class_uses',
                    // enum_exists('EnumName');
                    'enum_exists',
                    // get_class_methods('ClassName');
                    'get_class_methods',
                    // get_class_vars('ClassName');
                    'get_class_vars',
                    // get_parent_class('ClassName');
                    'get_parent_class',
                    // interface_exists('InterfaceName');
                    'interface_exists',
                    // is_subclass_of('ClassName', 'ParentClassName');
                    'is_subclass_of',
                    // trait_exists('TraitName', true);
                    'trait_exists',
                ])
                && $this->hasFuncCallIndexStringArg($node, 0)
            ) {
                return true;
            }

            if (
                $this->isNames($node, [
                    // class_alias('ClassName', 'AliasClassName');
                    'class_alias',
                    // is_subclass_of('ClassName', 'ParentClassName');
                    'is_subclass_of',
                ])
                && $this->hasFuncCallIndexStringArg($node, 1)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \PhpParser\Node\Expr\FuncCall|\PhpParser\Node\Expr\Variable|\PhpParser\Node\Identifier|\PhpParser\Node\Name $node
     */
    private function shouldUpperSnakeName(Node $node): bool
    {
        $parent = $node->getAttribute('parent');
        $grandparent = $parent ? $parent->getAttribute('parent') : null;

        if (
            $node instanceof Identifier
            // Foo::class;
            && !$this->isName($node, 'class')
            && is_instance_of_any($parent, [
                // class Foo{public const CONST_NAME = 'const';}
                Const_::class,
                // Foo::CONST_NAME;
                ClassConstFetch::class,
            ])
        ) {
            return true;
        }

        if (
            $node instanceof FuncCall
            && $this->isNames($node, [
                // define('CONST_NAME', 'const');
                'define',
                // defined('CONST_NAME');
                'defined',
                // constant('Foo::CONST_NAME');
                'constant',
            ])
            && $this->hasFuncCallIndexStringArg($node, 0)
        ) {
            return true;
        }

        // CONST_NAME;
        // use const CONST_NAME;
        return $node instanceof Name
            && (
                $parent instanceof ConstFetch
                || (
                    $parent instanceof UseItem
                    && $grandparent instanceof Use_
                    && (
                        (Use_::TYPE_UNKNOWN === $grandparent->type && Use_::TYPE_CONSTANT === $parent->type)
                        || (Use_::TYPE_CONSTANT === $grandparent->type && Use_::TYPE_UNKNOWN === $parent->type)
                    )
                )
            );
    }

    /**
     * @param \PhpParser\Node\Expr\FuncCall|\PhpParser\Node\Expr\Variable|\PhpParser\Node\Identifier|\PhpParser\Node\Name $node
     */
    private function shouldLcfirstCamelName(Node $node): bool
    {
        $parent = $node->getAttribute('parent');

        // $varName;
        if ($node instanceof Variable && \is_string($node->name)) {
            return true;
        }

        if (
            $node instanceof Identifier
            && is_instance_of_any($parent, [
                // class Foo{public $propertyName;}
                Property::class,
                // class Foo{public int $propertyName;}
                PropertyItem::class,
                // class Foo{public function methodName(){}}
                ClassMethod::class,
                // $object->propertyName;
                PropertyFetch::class,
                // Foo::$propertyName;
                StaticPropertyFetch::class,
                // $object->methodName();
                MethodCall::class,
                // Foo::methodName();
                StaticCall::class,
            ])
        ) {
            return true;
        }

        if ($node instanceof FuncCall) {
            if (
                $this->isNames($node, [
                    // call_user_method('methodName', $object);
                    'call_user_method',
                    // call_user_method_array('methodName', $object);
                    'call_user_method_array',
                ])
                && $this->hasFuncCallIndexStringArg($node, 0)
            ) {
                return true;
            }

            if (
                $this->isNames($node, [
                    // method_exists($object, 'methodName');
                    'method_exists',
                    // property_exists($object, 'propertyName');
                    'property_exists',
                ])
                && $this->hasFuncCallIndexStringArg($node, 1)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param callable(string): string $renamer
     *
     * @return \Closure(string): string
     */
    private function wrapRenamer(callable $renamer, Node $node): \Closure
    {
        return fn (string $name): string => $renamer(
            (function (string $name) use ($node): string {
                if (Str::is($this->except, $name)) {
                    throw new RectorErrorException($this, "The name [$name] is skipped.", $node->getAttributes());
                }

                // if the name is all uppercase letters, convert it to lowercase letters.
                if (ctype_upper(preg_replace('/[^a-zA-Z]/', '', $name))) {
                    return Str::lower($name);
                }

                return $name;
            })($name)
        );
    }

    private function renameFuncCallIndexStringArg(Arg $argNode, callable $renamer): bool
    {
        $stringNode = $argNode->value;
        \assert($stringNode instanceof String_);

        $newValue = Str::of($stringNode->value)
            ->explode('\\')
            ->pipe(
                static fn (Collection $collection): string => $collection
                    ->slice(0, -1)
                    ->push($renamer($collection->last()))
                    ->implode('\\')
            );
        \assert(\is_string($newValue));

        if ($newValue !== $stringNode->value) {
            $stringNode->value = $newValue;

            return true;
        }

        return false;
    }

    private function hasFuncCallIndexStringArg(FuncCall $funcCallNode, int $index): bool
    {
        /**
         * @see \Rector\Application\NodeAttributeReIndexer::reIndexNodeAttributes()
         *
         * Re-index the args to ensure the index is correct and to avoid other Rectors from messing up the index.
         */
        $funcCallNode->args = array_values($funcCallNode->args);

        foreach ($funcCallNode->args as $idx => $argNode) {
            if (
                ($idx < $index && (!$argNode instanceof Arg || $argNode->name instanceof Identifier))
                || $idx > $index
            ) {
                return false;
            }

            if (
                $idx === $index
                && $argNode instanceof Arg
                && !$argNode->name instanceof Identifier
                && $argNode->value instanceof String_
            ) {
                return true;
            }
        }

        return false; // @codeCoverageIgnore
    }

    // private function hasFuncCallNameStringArg(FuncCall $funcCallNode, string $name): bool
    // {
    //     foreach ($funcCallNode->args as $argNode) {
    //         if (
    //             $argNode instanceof Arg
    //             && $argNode->name instanceof Identifier
    //             && $argNode->name->name === $name
    //             && $argNode->value instanceof String_
    //         ) {
    //             return true;
    //         }
    //     }
    //
    //     return false;
    // }
}
