<?php

/**
 * Copyright (c) 2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/rector-rules
 */
namespace Guanguans\RectorRulesTests\Rector\Namespace_\RemoveNamespaceRector\Fixture;

it('can get classes', function (): void {
    expect(classes())->toBeArray()->toBeTruthy();
})->group(__DIR__, __FILE__);

?>
-----
<?php

/**
 * Copyright (c) 2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/rector-rules
 */

it('can get classes', function (): void {
    expect(classes())->toBeArray()->toBeTruthy();
})->group(__DIR__, __FILE__);
