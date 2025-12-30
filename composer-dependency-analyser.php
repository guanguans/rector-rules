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

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

return (new Configuration)
    ->addPathsToScan(
        [
            // __DIR__.'/src/',
        ],
        false
    )
    ->addPathsToExclude([
        __DIR__.'/src/Support/ComposerScripts.php',
        // __DIR__.'/src/Rector/',
        __DIR__.'/tests/',
    ])
    ->ignoreUnknownClasses([
        // \SensitiveParameter::class,
    ])
    /** @see \ShipMonk\ComposerDependencyAnalyser\Analyser::CORE_EXTENSIONS */
    ->ignoreErrorsOnExtensions(
        [
            'ext-ctype',
            'ext-mbstring',
        ],
        [ErrorType::SHADOW_DEPENDENCY],
    )
    // ->ignoreErrorsOnPackageAndPaths(
    //     'ergebnis/php-cs-fixer-config',
    //     [
    //         __DIR__.'/src/Rector/Cases/Rule/FuncCallRule.php',
    //     ],
    //     [ErrorType::DEV_DEPENDENCY_IN_PROD]
    // )
    ->ignoreErrorsOnPackages(
        [
            // 'illuminate/support',
            // 'rector/rector',
        ],
        [ErrorType::UNUSED_DEPENDENCY]
    )
    ->ignoreErrorsOnPackages(
        [
            'nikic/php-parser',
            'illuminate/collections',
            // 'symfony/console',
            'webmozart/assert',
            'symfony/polyfill-php80',
            'symfony/polyfill-php81',
        ],
        [ErrorType::SHADOW_DEPENDENCY]
    );
