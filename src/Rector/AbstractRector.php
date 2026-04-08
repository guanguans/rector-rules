<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */
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
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

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
    /**
     * @throws \Symplify\RuleDocGenerator\Exception\PoorDocumentationException
     */
    final public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition($this->description(), $this->codeSamples());
    }

    protected function description(): string
    {
        return (string) \str(static::class)
            ->afterLast('\\')
            ->beforeLast('Rector')
            ->headline()
            ->lower()
            ->ucfirst();
    }

    /**
     * @return list<\Symplify\RuleDocGenerator\Contract\CodeSampleInterface>
     */
    abstract protected function codeSamples(): array;
}
