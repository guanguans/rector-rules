<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector\Fixture81;

final class DemoFile
{
    public function run()
    {
        $array = ['foo' => 'bar'];

        return array_merge($array, ['baz' => 'xxx']);
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
        $array = ['foo' => 'bar'];

        return [...$array, 'baz' => 'xxx'];
    }
}

?>
