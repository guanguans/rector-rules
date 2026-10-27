<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\String_\StringToClassConstantRector\Fixture;

final class Fixture
{
    public static function getSubscribedEvents()
    {
        return [
            'compiler.non_matching_value1' => 'value1',
            'compiler.non_matching_value2' => 'value2',
        ];
    }
}
