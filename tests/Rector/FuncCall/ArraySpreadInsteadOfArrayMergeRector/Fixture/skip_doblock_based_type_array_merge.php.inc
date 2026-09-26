<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector\Fixture;

class SkipDocblockBasedTypeArrayMerge
{
    /**
     * @param array<int, string> $iter1
     * @param array<int, string> $iter2
     */
    public function run($iter1, $iter2)
    {
        $values = array_merge($iter1, $iter2);
    }
}
