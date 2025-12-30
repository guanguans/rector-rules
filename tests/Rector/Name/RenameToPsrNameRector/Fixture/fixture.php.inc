<?php
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
?>
-----
<?php
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
?>
