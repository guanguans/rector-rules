<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector\Fixture81;

final class SkipIteratorToArray
{
    public function run($items)
    {
        $values = iterator_to_array($items);
    }
}

?>
