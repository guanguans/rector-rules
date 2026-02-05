<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\File\SortFileFunctionStmtRector\Fixture;

function c(): void {}

function b(): void {}

function a(): void {}

?>
-----
<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\File\SortFileFunctionStmtRector\Fixture;

function a(): void {}

function b(): void {}

function c(): void {}

?>
