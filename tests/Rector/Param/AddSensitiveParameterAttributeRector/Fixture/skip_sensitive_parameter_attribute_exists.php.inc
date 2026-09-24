<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\Param\AddSensitiveParameterAttributeRector\Fixture;

class SkipSensitiveParameterAttributeExists
{
    public function login(
        $username,
        #[\SensitiveParameter] $password
    ): void {
    }
}
