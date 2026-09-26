<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\FuncCall\JsonThrowOnErrorRector\Fixture;

function skipNamedArgumentValueOrJson($str, array $data){
    json_decode(json: $str);
    json_encode(value: $data);
}
