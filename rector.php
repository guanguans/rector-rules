<?php

/** @noinspection PhpDeprecationInspection */
/** @noinspection PhpInternalEntityUsedInspection */
/** @noinspection PhpMultipleClassDeclarationsInspection */
/** @noinspection PhpUnhandledExceptionInspection */
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

use Ergebnis\Rector\Rules\Arrays\SortAssociativeArrayByKeyRector;
use Guanguans\RectorRules\Rector\Array_\SortListItemOfSameScalarTypeRector;
use Guanguans\RectorRules\Rector\File\AddNoinspectionDocblockToFileFirstStmtRector;
use Guanguans\RectorRules\Rector\Name\RenameToConventionalCaseNameRector;
use PhpParser\NodeVisitor\ParentConnectingVisitor;
use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;
use Rector\CodeQuality\Rector\LogicalAnd\LogicalToBooleanRector;
use Rector\CodingStyle\Rector\ArrowFunction\StaticArrowFunctionRector;
use Rector\CodingStyle\Rector\Assign\SplitDoubleAssignRector;
use Rector\CodingStyle\Rector\ClassLike\NewlineBetweenClassLikeStmtsRector;
use Rector\CodingStyle\Rector\Closure\StaticClosureRector;
use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\CodingStyle\Rector\Encapsed\WrapEncapsedVariableInCurlyBracesRector;
use Rector\CodingStyle\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector;
use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassLike\RemoveAnnotationRector;
use Rector\DowngradePhp80\Rector\FuncCall\DowngradeStrContainsRector;
use Rector\DowngradePhp80\Rector\FuncCall\DowngradeStrEndsWithRector;
use Rector\DowngradePhp80\Rector\FuncCall\DowngradeStrStartsWithRector;
use Rector\DowngradePhp81\Rector\FuncCall\DowngradeArrayIsListRector;
use Rector\DowngradePhp85\Rector\FuncCall\DowngradeArrayFirstLastRector;
use Rector\EarlyReturn\Rector\If_\ChangeOrIfContinueToMultiContinueRector;
use Rector\EarlyReturn\Rector\Return_\ReturnBinaryOrToEarlyReturnRector;
use Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameVariableToMatchNewTypeRector;
use Rector\Php73\Rector\FuncCall\JsonThrowOnErrorRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Renaming\Rector\FuncCall\RenameFunctionRector;
use Rector\Set\ValueObject\DowngradeLevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Transform\Rector\Scalar\ScalarValueToConstFetchRector;
use Rector\ValueObject\PhpVersion;
use Rector\ValueObject\Visibility;
use Rector\Visibility\Rector\ClassMethod\ChangeMethodVisibilityRector;
use Rector\Visibility\ValueObject\ChangeMethodVisibility;
use function Guanguans\RectorRules\Support\classes;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/config/',
        __DIR__.'/src/',
        __DIR__.'/tests/',
        __DIR__.'/composer-bump',
    ])
    ->withRootFiles()
    ->withSkip([
        '**/Fixtures/*',
        __DIR__.'/_ide_helper.php',
        // __DIR__.'/tests.php',
    ])
    ->withCache(__DIR__.'/.build/rector/')
    // ->withoutParallel()
    ->withParallel()
    // ->withImportNames(importDocBlockNames: false, importShortClasses: false)
    ->withImportNames(true, false, false)
    // ->withImportNames(importNames: false)
    // ->withEditorUrl()
    ->withFluentCallNewLine()
    ->withTreatClassesAsFinal()
    // ->withAttributesSets(phpunit: true, all: true)
    // ->withComposerBased(phpunit: true/* , laravel: true */)
    ->withComposerBased(false, false, true)
    ->withPhpVersion(PhpVersion::PHP_74)
    // ->withDowngradeSets(php74: true)
    // ->withDowngradeSets(php74: true)
    // ->withPhpSets(php74: true)
    ->withPhp74Sets()
    // ->withPreparedSets(
    //     deadCode: true,
    //     codeQuality: true,
    //     codingStyle: true,
    //     typeDeclarations: true,
    //     typeDeclarationDocblocks: true,
    //     privatization: true,
    //     naming: true,
    //     instanceOf: true,
    //     earlyReturn: true,
    //     // carbon: true,
    // )
    ->withSets([
        Guanguans\RectorRules\Set\SetList::ALL,
        Guanguans\RectorRules\Set\SetList::LARAVEL_80,
        PHPUnitSetList::PHPUNIT_90,
        DowngradeLevelSetList::DOWN_TO_PHP_74,
        SetList::DEAD_CODE,
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::TYPE_DECLARATION,
        SetList::TYPE_DECLARATION_DOCBLOCKS,
        SetList::PRIVATIZATION,
        SetList::NAMING,
        SetList::INSTANCEOF,
        SetList::EARLY_RETURN,
        // SetList::CARBON,

        SetList::ASSERT,
        SetList::PHP_POLYFILLS,
        SetList::RECTOR_PRESET,
    ])
    ->withRules([
        // ArraySpreadInsteadOfArrayMergeRector::class,
        JsonThrowOnErrorRector::class,
        SortAssociativeArrayByKeyRector::class,
        StaticArrowFunctionRector::class,
        StaticClosureRector::class,
    ])
    ->withConfiguredRule(AddNoinspectionDocblockToFileFirstStmtRector::class, [
        '*/src/Rector/*Rector.php' => [
            'PhpMultipleClassDeclarationsInspection',
        ],
        '*/tests/*' => [
            'AnonymousFunctionStaticInspection',
            'NullPointerExceptionInspection',
            'PhpPossiblePolymorphicInvocationInspection',
            'PhpUndefinedClassInspection',
            'PhpUnhandledExceptionInspection',
            'PhpVoidFunctionResultUsedInspection',
            'StaticClosureCanBeUsedInspection',
        ],
    ])
    ->registerDecoratingNodeVisitor(ParentConnectingVisitor::class)
    ->withConfiguredRule(RenameToConventionalCaseNameRector::class, [
        'MIT',
    ])
    // ->withConfiguredRule(SortListItemOfSameScalarTypeRector::class, [
    //     'ignore_comment' => false,
    //     'ignore_docblock' => false,
    // ])
    ->withConfiguredRule(RemoveAnnotationRector::class, [
        'codeCoverageIgnore',
        'inheritDoc',
        'phpstan-ignore',
        'phpstan-ignore-next-line',
        'psalm-suppress',
    ])
    ->withConfiguredRule(
        ChangeMethodVisibilityRector::class,
        classes(static fn (string $class, string $file): bool => str_starts_with($class, 'Guanguans\RectorRules'))
            ->filter(static fn (ReflectionClass $reflectionClass): bool => $reflectionClass->isTrait())
            ->map(
                static fn (ReflectionClass $reflectionClass): array => collect($reflectionClass->getMethods(ReflectionMethod::IS_PRIVATE))
                    ->reject(static fn (ReflectionMethod $reflectionMethod): bool => $reflectionMethod->isFinal() || $reflectionMethod->isInternal())
                    ->map(static fn (ReflectionMethod $reflectionMethod): ChangeMethodVisibility => new ChangeMethodVisibility(
                        $reflectionClass->getName(),
                        $reflectionMethod->getName(),
                        Visibility::PROTECTED
                    ))
                    ->all()
            )
            ->flatten()
            // ->dd()
            ->all(),
    )
    ->withConfiguredRule(RenameFunctionRector::class, [
        'Pest\Faker\fake' => 'fake',
        'Pest\Faker\faker' => 'fake',
        'test' => 'it',
    ])
    ->withSkip([
        DowngradeArrayFirstLastRector::class,
        DowngradeArrayIsListRector::class,
        DowngradeStrContainsRector::class,
        DowngradeStrEndsWithRector::class,
        DowngradeStrStartsWithRector::class,
    ])
    ->withSkip([
        ChangeOrIfContinueToMultiContinueRector::class,
        EncapsedStringsToSprintfRector::class,
        ExplicitBoolCompareRector::class,
        LogicalToBooleanRector::class,
        NewlineAfterStatementRector::class,
        NewlineBetweenClassLikeStmtsRector::class,
        ReturnBinaryOrToEarlyReturnRector::class,
        SplitDoubleAssignRector::class,
        WrapEncapsedVariableInCurlyBracesRector::class,
    ])
    ->withSkip([
        RenameParamToMatchTypeRector::class => [
            __DIR__.'/src/Rector/*Rector.php',
        ],
        RenameVariableToMatchNewTypeRector::class => [
            __DIR__.'/src/Rector/Namespace_/RemoveNamespaceRector.php',
        ],
        ScalarValueToConstFetchRector::class => [
            __DIR__.'/src/Rector/Name/RenameToConventionalCaseNameRector.php',
        ],
        SortAssociativeArrayByKeyRector::class => [
            __DIR__.'/src/',
            __DIR__.'/tests/',
        ],
        StaticArrowFunctionRector::class => $staticClosureSkipPaths = [
            __DIR__.'/tests/',
        ],
        StaticClosureRector::class => $staticClosureSkipPaths,
    ]);
