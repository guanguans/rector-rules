<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\FuncCall\JsonThrowOnErrorRector\Fixture;

function skipNamedArgumentFlags($str){
    json_decode($str, flags: JSON_THROW_ON_ERROR);
}

