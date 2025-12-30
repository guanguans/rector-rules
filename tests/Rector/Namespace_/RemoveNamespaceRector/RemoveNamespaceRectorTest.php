<?php

declare(strict_types=1);

/**
 * Copyright (c) 2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/rector-rules
 */

namespace Guanguans\RectorRulesTests\Rector\Namespace_\RemoveNamespaceRector;

use Guanguans\RectorRulesTests\Rector\AbstractRectorTestCase;

final class RemoveNamespaceRectorTest extends AbstractRectorTestCase
{
    protected static function directory(): string
    {
        return __DIR__;
    }
}
