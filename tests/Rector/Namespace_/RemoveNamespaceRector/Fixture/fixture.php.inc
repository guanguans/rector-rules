<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\Namespace_\RemoveNamespaceRector\Fixture;

it('is true', function (): void {
    expect(true)->toBeTrue();
});

?>
-----
<?php

/** @noinspection ALL */

it('is true', function (): void {
    expect(true)->toBeTrue();
});
