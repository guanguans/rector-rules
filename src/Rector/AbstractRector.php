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

namespace Guanguans\RectorRules\Rector;

use Guanguans\RectorRules\Support\ComposerScripts;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PhpParser\Error;
use PhpParser\ErrorHandler\Collecting;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use function Guanguans\RectorRules\Support\classes;

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
    public function __destruct()
    {
        if (ComposerScripts::makeSymfonyStyle()->isDebug()) {
            ComposerScripts::makeSymfonyStyle()->comment(
                collect($this->makeCollecting()->getErrors())
                    ->map(static fn (Error $error): string => $error->getRawMessage())
                    ->unique()
                    ->all()
            );
        }
    }

    /**
     * @see https://github.com/rectorphp/rector-src/blob/main/scoper.php
     */
    final public function classes(): Collection
    {
        return classes(
            static fn (string $class): bool => Str::of($class)->startsWith('Rector\\')
                && Str::of($class)->endsWith([
                    'Factory',
                    'Resolver',
                    // 'er',
                ])
                && !Str::of($class)->contains([
                    '\\SwissKnife\\',
                    '\\TypePerfect\\',
                ])
        )
            ->sortKeys()
            // ->groupBy(fn (string $class) => str($class)->explode('\\')->get(1))
            ->keys();
    }

    protected function description(): string
    {
        return (string) Str::of(static::class)
            ->afterLast('\\')
            ->beforeLast('Rector')
            ->headline();
    }

    protected function makeCollecting(): Collecting
    {
        static $collecting;

        return $collecting ??= new Collecting;
    }
}
