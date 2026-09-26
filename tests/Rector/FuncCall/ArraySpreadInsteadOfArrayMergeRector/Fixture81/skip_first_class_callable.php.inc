<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector\Fixture81;

class SkipFirstClassCallable
{
    public function run()
    {
        array_merge(...);
    }
}
