<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\FuncCall\JsonThrowOnErrorRector\Fixture;

function SkipJsonLastErrorNext($str)
{
    json_decode($str);
    return json_last_error() === JSON_ERROR_NONE;
}

?>
