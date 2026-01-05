# 7 Rules Overview

<br>

## Categories

- [Array](#array) (2)

- [Class](#class) (1)

- [Declare](#declare) (1)

- [Name](#name) (1)

- [Namespace](#namespace) (1)

- [New](#new) (1)

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

```diff
 /** @noinspection ALL */
 namespace Guanguans\RectorRules\Rector\New_;

 use Guanguans\RectorRules\Rector\AbstractRector;
 use Rector\Contract\Rector\ConfigurableRectorInterface;
 use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;

 final class NewExceptionToNewAnonymousExtendsExceptionImplementsRector extends AbstractRector implements ConfigurableRectorInterface
 {
     protected function codeSamples(): array
     {
         return [
             new ConfiguredCodeSample(
                 <<<'PHP'
                 /** @noinspection ALL */
-                // new Exception('Testing');
+                new Exception('Testing');
                 PHP,
                 <<<'PHP'
                 /** @noinspection ALL */
-                // new class('Testing') extends \Exception implements \Guanguans\RectorRules\Contract\ThrowableContract
-                // {
-                // };
+                new class('Testing') extends \Exception implements \Guanguans\RectorRules\Contract\ThrowableContract
+                {
+                };
                 PHP,
-                [/*'Guanguans\RectorRules\Contract\ThrowableContract'*/],
+                ['Guanguans\RectorRules\Contract\ThrowableContract'],
             ),
         ];
     }
 }
```

<br>

## Class

### UpdateRectorRefactorParamDocblockFromNodeTypesRector

Update rector refactor param docblock from node types

- class: [`Guanguans\RectorRules\Rector\Class_\UpdateRectorRefactorParamDocblockFromNodeTypesRector`](../src/Rector/Class_/UpdateRectorRefactorParamDocblockFromNodeTypesRector.php)

```diff
 /** @noinspection ALL */
 namespace Guanguans\RectorRules\Rector\Class_;

 use Guanguans\RectorRules\Rector\AbstractRector;
 use PhpParser\Node;
 use PhpParser\Node\Stmt\Class_;

 final class UpdateRectorRefactorParamDocblockFromNodeTypesRector extends AbstractRector
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

```diff
 /** @noinspection ALL */
 namespace Guanguans\RectorRules\Rector\Name;

 use Guanguans\RectorRules\Rector\AbstractRector;
 use PhpParser\Node;
 use PhpParser\Node\Expr\FuncCall;
 use PhpParser\Node\Expr\Variable;
 use PhpParser\Node\Identifier;
 use PhpParser\Node\Name;

 final class RenameToPsrNameRector extends AbstractRector
 {
     public function getNodeTypes(): array
     {
         return [
             FuncCall::class,
             Identifier::class,
             Name::class,
             Variable::class,
         ];
     }

+    /**
+     * @param \PhpParser\Node\Expr\FuncCall|\PhpParser\Node\Expr\Variable|\PhpParser\Node\Identifier|\PhpParser\Node\Name $node
+     */
     public function refactor(Node $node): ?Node
     {
         return null;
     }
 }
```

<br>

## Declare

### AddNoinspectionsDocCommentToDeclareRector

Add noinspections doc comment to declare

:wrench: **configure it!**

- class: [`Guanguans\RectorRules\Rector\Declare_\AddNoinspectionsDocCommentToDeclareRector`](../src/Rector/Declare_/AddNoinspectionsDocCommentToDeclareRector.php)

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

## Name

### RenameToPsrNameRector

Rename to psr name

:wrench: **configure it!**

- class: [`Guanguans\RectorRules\Rector\Name\RenameToPsrNameRector`](../src/Rector/Name/RenameToPsrNameRector.php)

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

```diff
 /** @noinspection ALL */
 /**
  * Copyright (c) 2025-2026 guanguans<ityaozm@gmail.com>
  *
  * For the full copyright and license information, please view
  * the LICENSE file that was distributed with this source code.
  *
  * @see https://github.com/guanguans/rector-rules
  */
-namespace Guanguans\RectorRulesTests\Rector\Namespace_\RemoveNamespaceRector\Fixture;

 it('is true', function (): void {
     expect(true)->toBeTrue();
 });
```

<br>

## New

### NewExceptionToNewAnonymousExtendsExceptionImplementsRector

New exception to new anonymous extends exception implements

:wrench: **configure it!**

- class: [`Guanguans\RectorRules\Rector\New_\NewExceptionToNewAnonymousExtendsExceptionImplementsRector`](../src/Rector/New_/NewExceptionToNewAnonymousExtendsExceptionImplementsRector.php)

```diff
 /** @noinspection ALL */
-new Exception('Testing');
+new class('Testing') extends \Exception implements \Guanguans\RectorRules\Contract\ThrowableContract
+{
+};
```

<br>
