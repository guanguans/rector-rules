<?php

/** @noinspection PhpUnusedAliasInspection */
declare(strict_types=1);

/**
 * Copyright (c) 2025-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/rector-rules
 */

use Ergebnis\Rector\Rules\Expressions\Arrays\SortAssociativeArrayByKeyRector;
use Ergebnis\Rector\Rules\Expressions\CallLikes\RemoveNamedArgumentForSingleParameterRector;
use Ergebnis\Rector\Rules\Expressions\Matches\SortMatchArmsByConditionalRector;
use Ergebnis\Rector\Rules\Faker\GeneratorPropertyFetchToMethodCallRector;
use Ergebnis\Rector\Rules\Files\ReferenceNamespacedSymbolsRelativeToNamespacePrefixRector;
use Ergebnis\Rector\Rules\PHPUnit\ReplaceTestAttributeWithTestPrefixRector;
use Guanguans\RectorRules\Rector\Array_\SimplifyListIndexRector;
use Guanguans\RectorRules\Rector\Array_\SortListItemOfSameScalarTypeRector;
use Guanguans\RectorRules\Rector\ClassMethod\PrivateToProtectedVisibilityForTraitRector;
use Guanguans\RectorRules\Rector\File\SortFileFirstStmtDocblockRector;
use Guanguans\RectorRules\Rector\File\SortFileFunctionStmtRector;
use Guanguans\RectorRules\Rector\FunctionLike\RenameGarbageParamNameRector;
use Guanguans\RectorRules\Rector\Namespace_\RemoveNamespaceRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassLike\RemoveAnnotationRector;
use Rector\Php82\Rector\Param\AddSensitiveParameterAttributeRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__.'/../config.php');
    $rectorConfig->rules([
        PrivateToProtectedVisibilityForTraitRector::class,
        RemoveNamespaceRector::class,
        RenameGarbageParamNameRector::class,
        SimplifyListIndexRector::class,
        SortFileFirstStmtDocblockRector::class,
        SortFileFunctionStmtRector::class,
        // SortListItemOfSameScalarTypeRector::class,
    ]);

    // $rectorConfig->ruleWithConfiguration(AddSensitiveParameterAttributeRector::class, [
    //     AddSensitiveParameterAttributeRector::SENSITIVE_PARAMETERS => [
    //         'accessToken',
    //         'apiKey',
    //         'botApiKey',
    //         'key',
    //         'password',
    //         'pushKey',
    //         'secret',
    //         'tempKey',
    //         'token',
    //         'webHook',
    //     ],
    // ]);

    // $rectorConfig->ruleWithConfiguration(RemoveAnnotationRector::class, [
    //     'codeCoverageIgnore',
    //     'inheritDoc',
    //     'phpstan-ignore',
    //     'phpstan-ignore-next-line',
    //     'psalm-suppress',
    // ]);

    if (class_exists(SortAssociativeArrayByKeyRector::class)) {
        $rectorConfig->rules([
            GeneratorPropertyFetchToMethodCallRector::class,
            RemoveNamedArgumentForSingleParameterRector::class,
            ReplaceTestAttributeWithTestPrefixRector::class,
            SortAssociativeArrayByKeyRector::class,
            SortMatchArmsByConditionalRector::class,
        ]);

        // $rectorConfig->ruleWithConfiguration(ReferenceNamespacedSymbolsRelativeToNamespacePrefixRector::class, [
        //     'namespacePrefixes' => [
        //         // 'Guanguans\\RectorRules',
        //     ],
        // ]);
    }
};
