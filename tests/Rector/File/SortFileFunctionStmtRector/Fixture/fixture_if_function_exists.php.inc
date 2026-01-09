<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\File\SortFileFunctionStmtRector\Fixture;

use function function_exists;

if (!function_exists('Guanguans\RectorRulesTests\Rector\File\SortFileFunctionStmtRector\Fixture\c')) {
    function c(): void
    {
    }
}

function b(): void
{
}

if (!function_exists('Guanguans\RectorRulesTests\Rector\File\SortFileFunctionStmtRector\Fixture\a')) {
    function a(): void
    {
    }
}

?>
-----
<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\File\SortFileFunctionStmtRector\Fixture;

use function function_exists;

if (!function_exists('Guanguans\RectorRulesTests\Rector\File\SortFileFunctionStmtRector\Fixture\a')) {
    function a(): void
    {
    }
}
function b(): void
{
}
if (!function_exists('Guanguans\RectorRulesTests\Rector\File\SortFileFunctionStmtRector\Fixture\c')) {
    function c(): void
    {
    }
}

?>
