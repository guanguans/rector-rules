<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\Param\AddSensitiveParameterAttributeRector\Fixture;

function login($username, $password): void
{
}

class ApplyAttributeToMethods
{
    public function login($username, $password): void
    {
    }
}
?>
-----
<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\Param\AddSensitiveParameterAttributeRector\Fixture;

function login($username, #[\SensitiveParameter]
$password): void
{
}

class ApplyAttributeToMethods
{
    public function login($username, #[\SensitiveParameter]
    $password): void
    {
    }
}
?>
