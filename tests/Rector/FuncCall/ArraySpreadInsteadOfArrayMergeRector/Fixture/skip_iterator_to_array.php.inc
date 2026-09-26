<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector\Fixture;

final class SkipIteratorToArray
{
    public function run($items)
    {
        $values = iterator_to_array($items);
    }
}

?>
