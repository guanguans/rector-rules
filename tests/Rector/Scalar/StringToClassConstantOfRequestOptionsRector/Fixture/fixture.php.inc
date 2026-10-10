<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\Scalar\StringToClassConstantOfRequestOptionsRector\Fixture;

$allowRedirects = 'allow_redirects';
$auth = 'auth';
$body = 'body';

?>
-----
<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\Scalar\StringToClassConstantOfRequestOptionsRector\Fixture;

$allowRedirects = \GuzzleHttp\RequestOptions::ALLOW_REDIRECTS;
$auth = \GuzzleHttp\RequestOptions::AUTH;
$body = \GuzzleHttp\RequestOptions::BODY;

?>
