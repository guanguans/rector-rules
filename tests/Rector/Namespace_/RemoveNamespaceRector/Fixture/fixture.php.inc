<?php

namespace Guanguans\RectorRulesTests\Rector\Namespace_\RemoveNamespaceRector\Fixture;

it('can get classes', function (): void {
    expect(classes())->toBeArray()->toBeTruthy();
})->group(__DIR__, __FILE__);

?>
-----
<?php

it('can get classes', function (): void {
    expect(classes())->toBeArray()->toBeTruthy();
})->group(__DIR__, __FILE__);

?>
