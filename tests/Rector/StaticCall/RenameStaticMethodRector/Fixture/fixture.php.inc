<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\StaticCall\RenameStaticMethodRector\Fixture;

function staticMethodNameReplacer()
{
    \Nette\Utils\Html::add('someContent');
    $result = FormMacros::renderFormBegin($someArgs);
    $result = FormMACROS::renderFormBEGIN($someArgs);
}

?>
-----
<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\StaticCall\RenameStaticMethodRector\Fixture;

function staticMethodNameReplacer()
{
    \Nette\Utils\Html::addHtml('someContent');
    $result = FormMacros::renderFormBegin($someArgs);
    $result = FormMACROS::renderFormBEGIN($someArgs);
}

?>
