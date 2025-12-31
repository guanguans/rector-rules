<?php

namespace Guanguans\RectorRulesTests\Rector\Namespace_\RemoveNamespaceRector\Fixture;

if (!\function_exists('Guanguans\RectorRulesTests\Rector\Namespace_\RemoveNamespaceRector\Fixture\add')) {
    function add(int $a, int $b): int {
        return $a + $b;
    }
}

?>
