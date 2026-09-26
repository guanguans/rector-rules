<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector\Fixture81;

final class DemoFile
{
    public function run()
    {
        return array_merge(['foo' => 'bar'], ['baz' => 'xxx']);
    }
}

?>
-----
<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector\Fixture81;

final class DemoFile
{
    public function run()
    {
        return ['foo' => 'bar', 'baz' => 'xxx'];
    }
}

?>
