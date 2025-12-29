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
use PhpParser\Error;
use PhpParser\ErrorHandler\Collecting;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;

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

    protected function makeCollecting(): Collecting
    {
        static $collecting;

        return $collecting ??= new Collecting;
    }
}
