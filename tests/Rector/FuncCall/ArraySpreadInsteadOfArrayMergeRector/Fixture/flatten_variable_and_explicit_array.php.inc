<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector\Fixture;

final class DemoFile
{
    public function run()
    {
        $array = ['foo'];

        return array_merge($array, ['bar']);
    }
}

?>
-----
<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector\Fixture;

final class DemoFile
{
    public function run()
    {
        $array = ['foo'];

        return [...$array, 'bar'];
    }
}

?>
