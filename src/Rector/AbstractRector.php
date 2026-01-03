<?php

declare(strict_types=1);

/**
 * Copyright (c) 2025-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/rector-rules
 */

namespace Guanguans\RectorRules\Rector;

use Illuminate\Support\Str;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;

/**
 * @see https://github.com/driftingly/rector-laravel
 * @see https://github.com/epifrin/rector-custom-rules
 * @see https://github.com/ergebnis/rector-rules
 * @see https://github.com/ingenerator/risky-rector-rules
 * @see https://github.com/MrPunyapal/rector-pest
 * @see https://github.com/nikic/PHP-Parser
 * @see https://github.com/rectorphp/rector
 * @see https://github.com/rectorphp/rector-src
 * @see https://github.com/savinmikhail/AddNamedArgumentsRector
 */
abstract class AbstractRector extends \Rector\Rector\AbstractRector implements DocumentedRuleInterface
{
    protected function description(): string
    {
        return (string) Str::of(static::class)
            ->afterLast('\\')
            ->beforeLast('Rector')
            ->headline()
            ->lower()
            ->ucfirst();
    }
}
