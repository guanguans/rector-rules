# 11 Rules Overview

<br>

## Categories

- [Array](#array) (3)

- [Class](#class) (2)

- [File](#file) (3)

- [FunctionLike](#functionlike) (1)

- [Name](#name) (1)

- [Namespace](#namespace) (1)

<br>

## Array

### SimplifyListIndexRector

Simplify list index

- class: [`Guanguans\RectorRules\Rector\Array_\SimplifyListIndexRector`](../src/Rector/Array_/SimplifyListIndexRector.php)

```diff
 /** @noinspection ALL */
-[0 => 'foo', 1 => 'bar', 2 => 'baz'];
-[0 => 'foo', 'bar', 2 => 'baz'];
+['foo', 'bar', 'baz'];
+['foo', 'bar', 'baz'];
```

<br>

### SortListItemOfSameScalarTypeRector

Sort list item of same scalar type

:wrench: **configure it!**

- class: [`Guanguans\RectorRules\Rector\Array_\SortListItemOfSameScalarTypeRector`](../src/Rector/Array_/SortListItemOfSameScalarTypeRector.php)

```diff
 /** @noinspection ALL */
 [
-    'c',
-    'b',
+    'A',
+    'C',
     'a',
-    'a10',
     'a8',
     'a9',
-    'C',
-    'A',
+    'a10',
+    'b',
+    'c',
 ];
```

<br>

### UpdateRectorCodeSamplesFromFixturesRector

Update rector code samples from fixtures

- class: [`Guanguans\RectorRules\Rector\Array_\UpdateRectorCodeSamplesFromFixturesRector`](../src/Rector/Array_/UpdateRectorCodeSamplesFromFixturesRector.php)

```diff
 /** @noinspection ALL */
 namespace Guanguans\RectorRules\Rector\Array_;

 use Guanguans\RectorRules\Rector\AbstractRector;
 use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;

 final class SimplifyListIndexRector extends AbstractRector
 {
     protected function codeSamples(): array
     {
         return [
             new CodeSample(
                 <<<'PHP'
                 /** @noinspection ALL */
                 [0 => 'foo', 1 => 'bar', 2 => 'baz'];
-                // [0 => 'foo', 'bar', 2 => 'baz'];
+                [0 => 'foo', 'bar', 2 => 'baz'];
                 PHP,
                 <<<'PHP'
                 /** @noinspection ALL */
                 ['foo', 'bar', 'baz'];
-                // ['foo', 'bar', 'baz'];
+                ['foo', 'bar', 'baz'];
                 PHP,
             ),
         ];
     }
 }
```

<br>

## Class

### UpdatePHPStanMethodNodeParamDocblockFromNodeTypesRector

Update p h p stan method node param docblock from node types

- class: [`Guanguans\RectorRules\Rector\Class_\UpdatePHPStanMethodNodeParamDocblockFromNodeTypesRector`](../src/Rector/Class_/UpdatePHPStanMethodNodeParamDocblockFromNodeTypesRector.php)

```diff
 /** @noinspection ALL */
 namespace Guanguans\PHPStanRules\Rule\File;

 use Guanguans\PHPStanRules\Rule\AbstractRule;
 use PhpParser\Node;
 use PHPStan\Analyser\Scope;
 use PHPStan\Node\FileNode;

 final class ForbiddenSideEffectsRule extends AbstractRule
 {
     public function getNodeType(): string
     {
         return FileNode::class;
     }

+    /**
+     * @param \PHPStan\Node\FileNode $node
+     */
     public function processNode(Node $node, Scope $scope): array
     {
         return [];
     }
 }
```

<br>

### UpdateRectorMethodNodeParamDocblockFromNodeTypesRector

Update rector method node param docblock from node types

- class: [`Guanguans\RectorRules\Rector\Class_\UpdateRectorMethodNodeParamDocblockFromNodeTypesRector`](../src/Rector/Class_/UpdateRectorMethodNodeParamDocblockFromNodeTypesRector.php)

```diff
 /** @noinspection ALL */
 namespace Guanguans\RectorRules\Rector\Class_;

 use Guanguans\RectorRules\Rector\AbstractRector;
 use PhpParser\Node;
 use PhpParser\Node\Stmt\Class_;

 final class UpdateRectorMethodNodeParamDocblockFromNodeTypesRector extends AbstractRector
 {
     public function getNodeTypes(): array
     {
         return [
             Class_::class,
         ];
     }

+    /**
+     * @param \PhpParser\Node\Stmt\Class_ $node
+     */
     public function refactor(Node $node): ?Node
     {
         return null;
     }
 }
```

<br>

## File

### AddNoinspectionDocblockToFileFirstStmtRector

Add noinspection docblock to file first stmt

:wrench: **configure it!**

- class: [`Guanguans\RectorRules\Rector\File\AddNoinspectionDocblockToFileFirstStmtRector`](../src/Rector/File/AddNoinspectionDocblockToFileFirstStmtRector.php)

```diff
 /** @noinspection AnonymousFunctionStaticInspection */
+/** @noinspection NullPointerExceptionInspection */
+/** @noinspection PhpPossiblePolymorphicInvocationInspection */
+/** @noinspection PhpUndefinedClassInspection */
+/** @noinspection PhpUnhandledExceptionInspection */
+/** @noinspection PhpVoidFunctionResultUsedInspection */
 /** @noinspection StaticClosureCanBeUsedInspection */
 /** @noinspection ALL */
 /** @noinspection PhpUnusedAliasInspection */
 declare(strict_types=1);
```

<br>

### SortFileFirstStmtDocblockRector

Sort file first stmt docblock

- class: [`Guanguans\RectorRules\Rector\File\SortFileFirstStmtDocblockRector`](../src/Rector/File/SortFileFirstStmtDocblockRector.php)

```diff
 /** @noinspection ALL */
-
+/** @noinspection NullPointerExceptionInspection */
+/** @noinspection PhpPossiblePolymorphicInvocationInspection */
+/** @noinspection StaticClosureCanBeUsedInspection */
 /**
  * Copyright (c) 2025-2026 guanguans<ityaozm@gmail.com>
  *
  * For the full copyright and license information, please view
  * the LICENSE file that was distributed with this source code.
  *
  * @see https://github.com/guanguans/rector-rules
  */
-
-/** @noinspection StaticClosureCanBeUsedInspection */
-/** @noinspection NullPointerExceptionInspection */
-/** @noinspection PhpPossiblePolymorphicInvocationInspection */
 declare(strict_types=1);
```

<br>

### SortFileFunctionStmtRector

Sort file function stmt

- class: [`Guanguans\RectorRules\Rector\File\SortFileFunctionStmtRector`](../src/Rector/File/SortFileFunctionStmtRector.php)

```diff
 /** @noinspection ALL */
 namespace Guanguans\RectorRulesTests\Rector\File\SortFileFunctionStmtRector\Fixture;

-function c(): void {}
+function a(): void {}
 function b(): void {}
-function a(): void {}
+function c(): void {}
```

<br>

## FunctionLike

### RenameGarbageVariableNameRector

Rename garbage variable name

- class: [`Guanguans\RectorRules\Rector\FunctionLike\RenameGarbageVariableNameRector`](../src/Rector/FunctionLike/RenameGarbageVariableNameRector.php)

```diff
 /** @noinspection ALL */
-collect($array)->filter(static function (string $value, int $key): bool {
+collect($array)->filter(static function (string $_, int $key): bool {
     return 2 === $key;
 });

 /**
- * @param mixed $value
+ * @param mixed $_
  */
-function filter($value, $key): bool
+function filter($_, $key): bool
 {
     return 2 === $key;
 }

 /**
  * @param mixed $key
  */
-function filter($value, $key): bool
+function filter($_, $key): bool
 {
     return 2 === $key;
 }

 function array_is_list(array $array): bool
 {
     $nextKey = -1;

-    foreach ($array as $key => $value) {
+    foreach ($array as $key => $_) {
         if ($key !== ++$nextKey) {
             return false;
         }
     }

     return true;
 }
```

<br>

## Name

### RenameToConventionalCaseNameRector

Rename to conventional case name

:wrench: **configure it!**

- class: [`Guanguans\RectorRules\Rector\Name\RenameToConventionalCaseNameRector`](../src/Rector/Name/RenameToConventionalCaseNameRector.php)

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
+constant('FOO::CONST_NAME');
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

<br>

## Namespace

### RemoveNamespaceRector

Remove namespace

- class: [`Guanguans\RectorRules\Rector\Namespace_\RemoveNamespaceRector`](../src/Rector/Namespace_/RemoveNamespaceRector.php)

```diff
 /** @noinspection ALL */
-namespace Guanguans\RectorRulesTests\Rector\Namespace_\RemoveNamespaceRector\Fixture;

 it('is true', function (): void {
     expect(true)->toBeTrue();
 });
```

<br>
