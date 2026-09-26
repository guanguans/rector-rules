<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector\Fixture81;

use stdClass;

class SkipSpreadArrayMerge
{
    public function run()
    {
        $values = [
            [new stdClass()],
            [new stdClass]
        ];

        $items = array_merge(...$values);

        return array_merge($items, ...$values);
    }
}
