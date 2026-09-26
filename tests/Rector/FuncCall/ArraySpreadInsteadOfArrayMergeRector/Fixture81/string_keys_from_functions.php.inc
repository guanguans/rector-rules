<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector\Fixture81;

function x(): array
{
    return ['b'=>1];
}
function y(): array
{
    return ['a'=>1];
}

class StringKeysFromFunctions
{
    public function run()
    {
        return array_merge(y(), x());
    }
}

?>
-----
<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector\Fixture81;

function x(): array
{
    return ['b'=>1];
}
function y(): array
{
    return ['a'=>1];
}

class StringKeysFromFunctions
{
    public function run()
    {
        return [...y(), ...x()];
    }
}

?>
