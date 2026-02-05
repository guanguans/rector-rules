<?php

/** @noinspection ALL */
/**
 * Copyright (c) 2025-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/rector-rules
 */

namespace Guanguans\RectorRulesTests\Rector\Namespace_\RemoveNamespaceRector\Fixture;

use Guanguans\Notify\DingTalk\Authenticator;
use Guanguans\Notify\DingTalk\Client;
use Guanguans\Notify\DingTalk\Messages\TextMessage;

beforeEach(function (): void {
    $this->client = new Client(new Authenticator);
});

it('can send text message', function (): void {
    $textMessage = TextMessage::make([]);

    expect($this->client)->assertCanSendMessage($textMessage);
})->group(__DIR__, __FILE__);

it('is true', function (): void {
    expect(true)->toBeTrue();
});

?>
-----
<?php

/** @noinspection ALL */
/**
 * Copyright (c) 2025-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/rector-rules
 */

use Guanguans\Notify\DingTalk\Authenticator;
use Guanguans\Notify\DingTalk\Client;
use Guanguans\Notify\DingTalk\Messages\TextMessage;

beforeEach(function (): void {
    $this->client = new Client(new Authenticator);
});

it('can send text message', function (): void {
    $textMessage = TextMessage::make([]);

    expect($this->client)->assertCanSendMessage($textMessage);
})->group(__DIR__, __FILE__);

it('is true', function (): void {
    expect(true)->toBeTrue();
});
