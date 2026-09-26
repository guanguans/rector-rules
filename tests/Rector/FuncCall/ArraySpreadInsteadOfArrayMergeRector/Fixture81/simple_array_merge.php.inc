<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector\Fixture81;

class SimpleArrayMerge
{
    public function run($iter1, $iter2)
    {
        $values = array_merge($iter1, $iter2);
    }
}

?>
-----
<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector\Fixture81;

class SimpleArrayMerge
{
    public function run($iter1, $iter2)
    {
        $values = [...$iter1, ...$iter2];
    }
}

?>
