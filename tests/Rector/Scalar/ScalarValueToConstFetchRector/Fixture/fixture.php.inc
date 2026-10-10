<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\Scalar\ScalarValueToConstFetchRector\Fixture;

$int = 10;
$float = 10.1;
$string = 'ABC';

?>
-----
<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\Scalar\ScalarValueToConstFetchRector\Fixture;

$int = \Guanguans\RectorRulesTests\Rector\Scalar\ScalarValueToConstFetchRector\Source\ClassWithConst::FOOBAR_INT;
$float = \Guanguans\RectorRulesTests\Rector\Scalar\ScalarValueToConstFetchRector\Source\ClassWithConst::FOOBAR_FLOAT;
$string = \Guanguans\RectorRulesTests\Rector\Scalar\ScalarValueToConstFetchRector\Source\ClassWithConst::FOOBAR_STRING;

?>
