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
        __DIR__.'/src/Support/Rector/',
        __DIR__.'/tests/',
        __DIR__.'/vendor/friendsofphp/php-cs-fixer/tests/',
    ])
    ->ignoreUnknownClasses([
        // \SensitiveParameter::class,
    ])
    /** @see \ShipMonk\ComposerDependencyAnalyser\Analyser::CORE_EXTENSIONS */
    ->ignoreErrorsOnExtensions(
        [
            // 'ext-mbstring',
            // 'ext-tokenizer',
        ],
        [ErrorType::SHADOW_DEPENDENCY],
    )
    // ->ignoreErrorsOnPackageAndPaths(
    //     'phpmyadmin/sql-parser',
    //     [
    //         __DIR__.'/src/Fixer/InlineHtml/SqlOfPhpmyadminSqlParserFixer.php',
    //     ],
    //     [ErrorType::DEV_DEPENDENCY_IN_PROD]
    // )
    ->ignoreErrorsOnPackages(
        [
            'illuminate/support',
            'rector/rector',
        ],
        [ErrorType::UNUSED_DEPENDENCY]
    )
    ->ignoreErrorsOnPackages(
        [
            'illuminate/collections',
            // 'symfony/console',
            // 'symfony/polyfill-php80',
        ],
        [ErrorType::SHADOW_DEPENDENCY]
    );
