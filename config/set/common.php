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
use Guanguans\RectorRules\NodeVisitor\ParentConnectingVisitor;
use Guanguans\RectorRules\Rector\Array_\SimplifyListIndexRector;
use Guanguans\RectorRules\Rector\Array_\SortListItemOfSameScalarTypeRector;
use Guanguans\RectorRules\Rector\ClassLike\RemoveAnnotationRector;
use Guanguans\RectorRules\Rector\ClassMethod\PrivateToProtectedVisibilityForTraitRector;
use Guanguans\RectorRules\Rector\File\AddNoinspectionDocblockToFileFirstStmtRector;
use Guanguans\RectorRules\Rector\File\SortFileFirstStmtDocblockRector;
use Guanguans\RectorRules\Rector\File\SortFileFunctionStmtRector;
use Guanguans\RectorRules\Rector\FunctionLike\RenameGarbageParamNameRector;
use Guanguans\RectorRules\Rector\Name\RenameToConventionalCaseNameRector;
use Guanguans\RectorRules\Rector\Namespace_\RemoveNamespaceRector;
use Guanguans\RectorRules\Rector\Param\AddSensitiveParameterAttributeRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__.'/../config.php');
    $rectorConfig->rules([
        PrivateToProtectedVisibilityForTraitRector::class,
        RemoveNamespaceRector::class,
        RenameGarbageParamNameRector::class,
        SimplifyListIndexRector::class,
        SortFileFirstStmtDocblockRector::class,
        SortFileFunctionStmtRector::class,
    ]);

    $rectorConfig->ruleWithConfiguration(AddNoinspectionDocblockToFileFirstStmtRector::class, [
        '*/Mixin/*' => $mixinInspections = [
            'OverrideMissingInspection',
        ],
        '*/Mixins/*' => $mixinInspections,
        '*/tests/*' => [
            'AnonymousFunctionStaticInspection',
            'NullPointerExceptionInspection',
            'PhpFieldAssignmentTypeMismatchInspection',
            'PhpPossiblePolymorphicInvocationInspection',
            'PhpUndefinedClassInspection',
            'PhpUnhandledExceptionInspection',
            'PhpVoidFunctionResultUsedInspection',
            'SqlResolve',
            'StaticClosureCanBeUsedInspection',
        ],
        '*/tests/Arch/*' => [
            'ClassConstantCanBeUsedInspection',
            'ClassnameLiteralInspection',
        ],
        '*Rector.php' => [
            'PhpMultipleClassDeclarationsInspection',
        ],
    ]);

    $rectorConfig->ruleWithConfiguration(AddSensitiveParameterAttributeRector::class, [
        AddSensitiveParameterAttributeRector::SENSITIVE_PARAMETERS => [
            'accessToken',
            'apiKey',
            'botApiKey',
            'clientSecret',
            // 'key',
            'password',
            'pushKey',
            'secret',
            'tempKey',
            'token',
            'usernameOrToken',
            'webHook',
        ],
    ]);

    $rectorConfig->ruleWithConfiguration(RemoveAnnotationRector::class, [
        'codeCoverageIgnore',
        'inheritDoc',
        'phpstan-ignore',
        'phpstan-ignore-next-line',
        'psalm-suppress',
    ]);

    $rectorConfig->bound(ParentConnectingVisitor::class) or $rectorConfig->singleton(ParentConnectingVisitor::class);
    $rectorConfig->ruleWithConfiguration(RenameToConventionalCaseNameRector::class, [
        'Debug', 'Info', 'Notice', 'Warning', 'Error', 'Critical', 'Alert', 'Emergency', /** @see \Monolog\Level */
        'DB',
        'MIT',
        'PDO',
        'URL',
        'Value',
        'afterEach',
        'assertMatches*Snapshot',
        'beforeEach',
        'current_password',
        'extra_attributes',
        'new_password',
    ]);

    $rectorConfig->ruleWithConfiguration(SortListItemOfSameScalarTypeRector::class, [
        'ignore_comment' => false,
        'ignore_docblock' => false,
    ]);

    /**
     * @required https://packagist.org/packages/ergebnis/rector-rules
     */
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
