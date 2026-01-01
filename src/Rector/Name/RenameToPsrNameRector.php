<?php

/** @noinspection PhpUnusedAliasInspection */
/** @noinspection EfferentObjectCouplingInspection */

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

use Guanguans\RectorRules\Rector\AbstractRector;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PhpParser\Error;
use PhpParser\Node;
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
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\EnumCase;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\UseItem;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\PHPStan\ScopeFetcher;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Webmozart\Assert\Assert;
use function Guanguans\RectorRules\Support\is_instance_of_any;

final class RenameToPsrNameRector extends AbstractRector implements ConfigurableRectorInterface
{
    /** @var list<string> */
    private array $except = [
        '_*',
        '*_',

        'class',
        'false',
        'null',
        'self',
        'static',
        'stdClass',
        'true',

        /**
         * @see https://www.php.net/manual/zh/reserved.variables.php
         */
        'GLOBALS',
        '_COOKIE',
        '_ENV',
        'HTTP_ENV_VARS',
        '_FILES',
        'HTTP_POST_FILES',
        '_GET',
        'HTTP_GET_VARS',
        '_POST',
        'HTTP_POST_VARS',
        '_REQUEST',
        '_SERVER',
        'HTTP_SERVER_VARS',
        '_SESSION',
        'HTTP_SESSION_VARS',
        'HTTP_RAW_POST_DATA',
        'http_response_header',
        'php_errormsg',

        /**
         * @see https://www.php.net/streamwrapper
         */
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

        /**
         * @see https://www.php.net/manual/zh/class.php-user-filter.php
         */
        'php_user_filter',
    ];

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
     * @param \PhpParser\Node\Expr\FuncCall|\PhpParser\Node\Expr\Variable|\PhpParser\Node\Identifier|\PhpParser\Node\Name $node
     *
     * @noinspection PhpDocSignatureInspection
     */
    public function refactor(Node $node): ?Node
    {
        // dump($node->getAttribute('parent'));
        try {
            if ($this->shouldLowerSnakeName($node)) {
                return $this->rename($node, static fn (string $name): string => Str::lower(Str::snake($name)));
            }

            if ($this->shouldUcfirstCamelName($node)) {
                return $this->rename($node, static fn (string $name): string => Str::ucfirst(Str::camel($name)));
            }

            if ($this->shouldUpperSnakeName($node)) {
                return $this->rename($node, static fn (string $name): string => Str::upper(Str::snake($name)));
            }

            if ($this->shouldLcfirstCamelName($node)) {
                // return $this->rename($node, static fn (string $name): string => Str::lcfirst(Str::camel($name)));
                return $this->rename($node, static fn (string $name): string => lcfirst(Str::camel($name)));
            }
        } catch (Error $error) {
            $this->makeCollecting()->handleError($error);
        }

        return null;
    }

    /**
     * @see https://github.com/barryvdh/laravel-ide-helper/blob/master/resources/views/meta.php
     *
     * @throws \Symplify\RuleDocGenerator\Exception\PoorDocumentationException
     * @throws \Symplify\RuleDocGenerator\Exception\ShouldNotHappenException
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            $this->description(),
            [
                new ConfiguredCodeSample(
                    <<<'CODE_SAMPLE'
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
                        #[attribute_name()]
                        class class_name{}
                        enum enum_name{}
                        enum Enum{case case_name;}
                        interface interface_name{}
                        trait trait_name{}
                        class Foo extends class_name implements interface_name{}
                        class_name::$property;
                        class_name::CONST;
                        class_name::method();
                        enum Enum implements interface_name{}
                        use class_name;
                        use trait_name;
                        class_alias('class_name', 'alias_class_name');
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
                        CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
                        // lower snake
                        function function_name(){}
                        function_name();
                        call_user_func('function_name');
                        call_user_func_array('function_name');
                        function_exists('function_name');

                        // ucfirst camel
                        class ClassName{}
                        enum EnumName{}
                        enum Enum{case CaseName;}
                        interface InterfaceName{}
                        trait TraitName{}
                        class Foo extends ClassName implements InterfaceName{}
                        ClassName::$property;
                        ClassName::CONST;
                        ClassName::method();
                        enum Enum implements InterfaceName{}
                        use ClassName;
                        use TraitName;
                        class_alias('ClassName', 'AliasClassName');
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
                        trait_exists('TraitName', true);

                        // upper snake
                        class Foo{public const CONST_NAME = 'const';}
                        Foo::CONST_NAME;
                        define('CONST_NAME', 'const');
                        defined('CONST_NAME');
                        constant('CONST_NAME');
                        constant('Foo::CONST_NAME');
                        CONST_NAME;

                        // lcfirst camel
                        $varName;
                        $object->methodName();
                        $object->propertyName;
                        class Foo{public $propertyName;}
                        class Foo{public function methodName(){}}
                        class Foo{public int $propertyName;}
                        Foo::$propertyName;
                        Foo::methodName();
                        call_user_method('methodName', $object);
                        call_user_method_array('methodName', $object);
                        method_exists($object, 'methodName');
                        property_exists($object, 'propertyName');
                        CODE_SAMPLE,
                    ['exceptName']
                ),
            ]
        );
    }

    public function configure(array $configuration): void
    {
        Assert::allStringNotEmpty($configuration);
        $this->except = array_merge($this->except, $configuration);
    }

    /**
     * @see \Rector\NodeNameResolver\NodeNameResolver
     * @see \Rector\Renaming\Collector\RenamedNameCollector
     * @see https://github.com/rectorphp/rector/issues/7611
     * @see https://github.com/rectorphp/rector/blob/main/UPGRADING.md#1-abstractscopeawarerector-is-removed-use-abstractrector-instead
     *
     * @param \PhpParser\Node\Expr\FuncCall|\PhpParser\Node\Expr\Variable|\PhpParser\Node\Identifier|\PhpParser\Node\Name $node
     *
     * @noinspection PhpDocSignatureInspection
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    private function rename(Node $node, callable $renamer): ?Node
    {
        $renamer = fn (string $name): string => $renamer((function (string $name) use ($node): string {
            if (Str::is($this->except, $name)) {
                throw new Error("The name [$name] is skipped.", $node->getAttributes());
            }

            if (ctype_upper(preg_replace('/[^a-zA-Z]/', '', $name))) {
                return mb_strtolower($name, 'UTF-8');
            }

            return $name;
        })($name));

        if ($node instanceof Name) {
            // return Name::concat($node->slice(0, -1), $renamer($node->getLast()));
            $node->name = Name::concat($node->slice(0, -1), $renamer($node->getLast()))->name;

            return $node;
        }

        if (
            is_instance_of_any($node, [
                Variable::class,
                Identifier::class,
            ])
        ) {
            $caseName = $renamer($node->name);

            if ($caseName === $node->name) {
                // return $node; // It's magical.
                return null;
            }

            $node->name = $caseName;
            // $node->setAttribute('scope', ScopeFetcher::fetch($node));

            return $node;
        }

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
                $node->args[0]->value->value = Str::of($node->args[0]->value->value)
                    ->explode('\\')
                    ->pipe(
                        static fn (Collection $collection): string => $collection
                            ->slice(0, -1)
                            ->push($renamer($collection->last()))
                            ->implode('\\')
                    );
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
                $node->args[1]->value->value = Str::of($node->args[1]->value->value)
                    ->explode('\\')
                    ->pipe(
                        static fn (Collection $collection): string => $collection
                            ->slice(0, -1)
                            ->push($renamer($collection->last()))
                            ->implode('\\')
                    );
            }
        }

        return $node;
    }

    /**
     * @param \PhpParser\Node\Expr\FuncCall|\PhpParser\Node\Expr\Variable|\PhpParser\Node\Identifier|\PhpParser\Node\Name $node
     *
     * @noinspection PhpDocSignatureInspection
     */
    private function shouldLowerSnakeName(Node $node): bool
    {
        $parent = $node->getAttribute('parent');
        $grandfather = $parent ? $parent->getAttribute('parent') : null;

        // function function_name(){}
        if ($node instanceof Identifier && $parent instanceof Function_) {
            return true;
        }

        // function_name();
        // use function function_name;
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        if (
            $node instanceof Name
            && (
                $parent instanceof FuncCall
                || (
                    is_instance_of_any($parent, [UseItem::class])
                    && (
                        (Use_::TYPE_UNKNOWN === $grandfather->type && Use_::TYPE_FUNCTION === $parent->type)
                        || (Use_::TYPE_FUNCTION === $grandfather->type && Use_::TYPE_UNKNOWN === $parent->type)
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
     *
     * @noinspection PhpDocSignatureInspection
     */
    private function shouldUcfirstCamelName(Node $node): bool
    {
        $parent = $node->getAttribute('parent');

        /** @noinspection PhpUnusedLocalVariableInspection */
        $grandfather = $parent ? $parent->getAttribute('parent') : null;

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
                /** @noinspection PhpConditionAlreadyCheckedInspection */
                (
                    is_instance_of_any($parent, [UseItem::class])
                    && (
                        (Use_::TYPE_UNKNOWN === $grandfather->type && Use_::TYPE_NORMAL === $parent->type)
                        || (Use_::TYPE_NORMAL === $grandfather->type && Use_::TYPE_UNKNOWN === $parent->type)
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
     *
     * @noinspection PhpDocSignatureInspection
     */
    private function shouldUpperSnakeName(Node $node): bool
    {
        $parent = $node->getAttribute('parent');
        $grandfather = $parent ? $parent->getAttribute('parent') : null;

        if (
            $node instanceof Identifier
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
        // use const CONST_NAME;;
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        return $node instanceof Name
            && (
                $parent instanceof ConstFetch
                || (
                    is_instance_of_any($parent, [UseItem::class])
                    && (
                        (Use_::TYPE_UNKNOWN === $grandfather->type && Use_::TYPE_CONSTANT === $parent->type)
                        || (Use_::TYPE_CONSTANT === $grandfather->type && Use_::TYPE_UNKNOWN === $parent->type)
                    )
                )
            );
    }

    /**
     * @param \PhpParser\Node\Expr\FuncCall|\PhpParser\Node\Expr\Variable|\PhpParser\Node\Identifier|\PhpParser\Node\Name $node
     *
     * @noinspection PhpDocSignatureInspection
     */
    private function shouldLcfirstCamelName(Node $node): bool
    {
        // $varName;
        if ($node instanceof Variable && \is_string($node->name)) {
            return true;
        }

        if (
            $node instanceof Identifier
            && is_instance_of_any($node->getAttribute('parent'), [
                // class Foo{public $propertyName;}
                Property::class,
                // class Foo{public int $propertyName;}
                PropertyProperty::class,
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

    private function hasFuncCallIndexStringArg(FuncCall $funcCall, int $index): bool
    {
        return isset($funcCall->args[$index])
            && null === $funcCall->args[$index]->name
            && $funcCall->args[$index]->value instanceof String_;
    }

    // private function hasFuncCallNameStringArg(FuncCall $funcCall, string $name): bool
    // {
    //     foreach ($funcCall->args as $arg) {
    //         if (
    //             $arg->name instanceof Identifier
    //             && $arg->name->name === $name
    //             && $arg->value instanceof String_
    //         ) {
    //             return true;
    //         }
    //     }
    //
    //     return false;
    // }
}
