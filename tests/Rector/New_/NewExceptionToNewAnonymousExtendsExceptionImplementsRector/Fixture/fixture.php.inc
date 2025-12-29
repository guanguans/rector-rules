<?php

namespace Guanguans\RectorRulesTests\Rector\New_\NewExceptionToNewAnonymousExtendsExceptionImplementsRector\Fixture;

new \Exception('Testing');

?>
-----
<?php

namespace Guanguans\RectorRulesTests\Rector\New_\NewExceptionToNewAnonymousExtendsExceptionImplementsRector\Fixture;

new class('Testing') extends \Exception implements \Guanguans\RectorRules\Contract\ThrowableContract
{
};

?>
