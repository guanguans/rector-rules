<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector\Fixture81;

final class DemoFile
{
    public function run()
    {
        $array = ['foo' => 'bar'];

        return array_merge(['baz' => 'xxx'], $array);
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

        return ['baz' => 'xxx', ...$array];
    }
}

?>
