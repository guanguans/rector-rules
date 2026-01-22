# rector-rules

> [!NOTE]
> A set of additional rules for rector/rector. - 一套针对 `rector/rector` 的附加规则。

[![tests](https://github.com/guanguans/rector-rules/actions/workflows/tests.yml/badge.svg)](https://github.com/guanguans/rector-rules/actions/workflows/tests.yml)
[![php-cs-fixer](https://github.com/guanguans/rector-rules/actions/workflows/php-cs-fixer.yml/badge.svg)](https://github.com/guanguans/rector-rules/actions/workflows/php-cs-fixer.yml)
[![codecov](https://codecov.io/gh/guanguans/rector-rules/graph/badge.svg?token=0RtgSGom4K)](https://codecov.io/gh/guanguans/rector-rules)
[![Latest Stable Version](https://poser.pugx.org/guanguans/rector-rules/v)](https://packagist.org/packages/guanguans/rector-rules)
[![GitHub release (with filter)](https://img.shields.io/github/v/release/guanguans/rector-rules)](https://github.com/guanguans/rector-rules/releases)
[![Total Downloads](https://poser.pugx.org/guanguans/rector-rules/downloads)](https://packagist.org/packages/guanguans/rector-rules)
[![License](https://poser.pugx.org/guanguans/rector-rules/license)](https://packagist.org/packages/guanguans/rector-rules)

## Requirement

* PHP >= 7.4

## Installation

```shell
composer require guanguans/rector-rules --dev --ansi -v
```

## Usage

### :monocle_face: [Rules Overview](docs/rules-overview.md)

### :monocle_face: [Sets Overview](config/set/)

* [`Guanguans\RectorRules\Set\SetList::ALL`](src/Set/SetList.php)
* [`Guanguans\RectorRules\Set\SetList::COMMON`](src/Set/SetList.php)
* [`Guanguans\RectorRules\Set\SetList::GUZZLE`](src/Set/SetList.php)
* [`Guanguans\RectorRules\Set\SetList::LARAVEL`](src/Set/SetList.php)
* [`Guanguans\RectorRules\Set\SetList::PEST`](src/Set/SetList.php)
* [`Guanguans\RectorRules\Set\SetList::PHPBENCH`](src/Set/SetList.php)
* [`Guanguans\RectorRules\Set\SetList::PHPSTAN`](src/Set/SetList.php)
* [`Guanguans\RectorRules\Set\SetList::RECTOR`](src/Set/SetList.php)
* [`Guanguans\RectorRules\Set\SetList::SYMFONY`](src/Set/SetList.php)

### In your rector configuration register rules

```php
use Guanguans\RectorRules\Rector\File\SortFileFunctionStmtRector;
use Guanguans\RectorRules\Rector\FunctionLike\RenameGarbageParamNameRector;
use Guanguans\RectorRules\Rector\Name\RenameToConventionalCaseNameRector;
use PhpParser\NodeVisitor\ParentConnectingVisitor;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withSets([
        Guanguans\RectorRules\Set\SetList::ALL,
        // ...
    ])
    // ...
    ->registerDecoratingNodeVisitor(ParentConnectingVisitor::class)
    ->withConfiguredRule(RenameToConventionalCaseNameRector::class, [
        'assertMatches*Snapshot', // Exclude `spatie/pest-plugin-snapshots` function name
        'beforeEach', // Exclude `pestphp/pest` function name
        'PDO', // Exclude `ext-pdo` class name
    ])
    // ...
    ->withRules([
        RenameGarbageParamNameRector::class,
        SortFileFunctionStmtRector::class,
        // ...
    ]);
```

### Example of RenameToConventionalCaseNameRector

```diff
 /** @noinspection ALL */
 // @formatter:off
 // phpcs:ignoreFile

 // lower snake
-use function functionName;
-function functionName(){}
-functionName();
-call_user_func('functionName');
-call_user_func_array('functionName', []);
-function_exists('functionName');
+use function function_name;
+function function_name(){}
+\function_name();
+call_user_func('function_name');
+call_user_func_array('function_name', []);
+function_exists('function_name');

 // ucfirst camel
 // #[attribute_name()]
-class class_name{}
+class ClassName{}
 // enum enum_name{}
 // enum Enum{case case_name;}
-interface interface_name{}
-trait trait_name{}
-class Foo extends class_name implements interface_name{}
-class_name::$property;
-class_name::CONST;
-class_name::method();
+interface InterfaceName{}
+trait TraitName{}
+class Foo extends \ClassName implements \InterfaceName{}
+\ClassName::$property;
+\ClassName::CONST;
+\ClassName::method();
 // enum Enum implements interface_name{}
-use class_name;
-use trait_name;
-class_alias('class_name', 'alias_class_name');
-class_alias($className, 'alias_class_name');
-class_exists('class_name');
-class_implements('class_name');
-class_parents('class_name');
-class_uses('class_name');
-enum_exists('enum_name');
-get_class_methods('class_name');
-get_class_vars('class_name');
-get_parent_class('class_name');
-interface_exists('interface_name');
-is_subclass_of('class_name', 'parent_class_name');
-is_subclass_of($className, 'parent_class_name');
-trait_exists('trait_name', true);
+use ClassName;
+use TraitName;
+class_alias('ClassName', 'AliasClassName');
+class_alias($className, 'AliasClassName');
+class_exists('ClassName');
+class_implements('ClassName');
+class_parents('ClassName');
+class_uses('ClassName');
+enum_exists('EnumName');
+get_class_methods('ClassName');
+get_class_vars('ClassName');
+get_parent_class('ClassName');
+interface_exists('InterfaceName');
+is_subclass_of('ClassName', 'ParentClassName');
+is_subclass_of($className, 'ParentClassName');
+trait_exists('TraitName', true);

 // upper snake
-use const constName;
-class Foo{public const constName = 'const';}
-Foo::constName;
-define('constName', 'const');
-defined('constName');
-constant('constName');
-constant('Foo::constName');
-constName;
+use const CONST_NAME;
+class Foo{public const CONST_NAME = 'const';}
+Foo::CONST_NAME;
+define('CONST_NAME', 'const');
+defined('CONST_NAME');
+constant('CONST_NAME');
+constant('Foo::CONST_NAME');
+\CONST_NAME;

 // lcfirst camel
-$var_name;
-$object->method_name();
-$object->property_name;
-call_user_method('method_name', $object);
-call_user_method_array('method_name', $object);
-class Foo{public $property_name;}
-class Foo{public function method_name(){}}
-class Foo{public int $property_name;}
-Foo::$property_name;
-Foo::method_name();
-method_exists($object, 'method_name');
-property_exists($object, 'property_name');
+$varName;
+$object->methodName();
+$object->propertyName;
+call_user_method('methodName', $object);
+call_user_method_array('methodName', $object);
+class Foo{public $propertyName;}
+class Foo{public function methodName(){}}
+class Foo{public int $propertyName;}
+Foo::$propertyName;
+Foo::methodName();
+method_exists($object, 'methodName');
+property_exists($object, 'propertyName');
```

## Composer scripts

```shell
composer checks:required
composer php-cs-fixer:fix
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

* [guanguans](https://github.com/guanguans)
* [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
