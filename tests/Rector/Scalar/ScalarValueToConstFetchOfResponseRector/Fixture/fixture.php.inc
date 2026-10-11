<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\Scalar\ScalarValueToConstFetchOfResponseRector\Fixture;

$httpContinue = 100;
$httpSwitchingProtocols = 101;
$httpProcessing = 102;

?>
-----
<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\Scalar\ScalarValueToConstFetchOfResponseRector\Fixture;

$httpContinue = \Symfony\Component\HttpFoundation\Response::HTTP_CONTINUE;
$httpSwitchingProtocols = \Symfony\Component\HttpFoundation\Response::HTTP_SWITCHING_PROTOCOLS;
$httpProcessing = \Symfony\Component\HttpFoundation\Response::HTTP_PROCESSING;

?>
