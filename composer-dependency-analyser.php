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

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;

return (new Configuration)
    // ->addPathsToScan([__DIR__.'/config/'], false)
    ->addPathsToExclude([
        __DIR__.'/src/Support/ComposerScripts.php',
        __DIR__.'/tests/',
    ])
    ->ignoreUnknownClasses([
        CodeSample::class,
    ])
    /** @see \ShipMonk\ComposerDependencyAnalyser\Analyser::CORE_EXTENSIONS */
    ->ignoreErrorsOnExtensions(
        [
            'ext-ctype',
        ],
        [ErrorType::SHADOW_DEPENDENCY],
    )
    ->ignoreErrorsOnPackages(
        [
            /**
             * @see https://github.com/rectorphp/rector-src/blob/main/scoper.php
             * @see vendor/rector/rector/vendor/symfony/
             */
            'illuminate/collections',
            'nikic/php-parser',
            'phpstan/phpstan',
        ],
        [ErrorType::SHADOW_DEPENDENCY]
    );
