<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector\Fixture81;

class StringKeys
{
    public function run()
    {
        $iter1 = ['one' => 'two', 'three' => 'four'];
        $iter2 = ['five' => 'six', 'seven' => 'eight'];

        return array_merge($iter1, $iter2);
    }

    public function go()
    {
        $iter1 = [1 => 'two', 3 => 'four'];
        $iter2 = ['five' => 'six', 'seven' => 'eight'];

        return array_merge($iter1, $iter2);
    }
}
?>
-----
<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector\Fixture81;

class StringKeys
{
    public function run()
    {
        $iter1 = ['one' => 'two', 'three' => 'four'];
        $iter2 = ['five' => 'six', 'seven' => 'eight'];

        return [...$iter1, ...$iter2];
    }

    public function go()
    {
        $iter1 = [1 => 'two', 3 => 'four'];
        $iter2 = ['five' => 'six', 'seven' => 'eight'];

        return [...$iter1, ...$iter2];
    }
}
?>
