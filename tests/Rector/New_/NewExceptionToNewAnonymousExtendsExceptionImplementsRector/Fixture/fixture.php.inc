<?php

/** @noinspection ALL */
new Exception('Testing');

?>
-----
<?php

/** @noinspection ALL */
new class('Testing') extends \Exception implements \Guanguans\RectorRules\Contract\ThrowableContract
{
};

?>
