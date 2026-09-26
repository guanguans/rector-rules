<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector\Fixture;

class SkipSimpleArrayMerge
{
    public function run($iter1, $iter2)
    {
        $values = array_merge($iter1, $iter2);
    }
}
