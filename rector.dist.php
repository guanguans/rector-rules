<?php

/** @noinspection PhpInternalEntityUsedInspection */
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
use Guanguans\PhpCsFixerCustomFixers\Support\Utils;
use Guanguans\RectorRules\NodeVisitor\ParentConnectingVisitor;
use Guanguans\RectorRules\Rector\File\AddNoinspectionDocblockToFileFirstStmtRector;
use Guanguans\RectorRules\Rector\Name\RenameToConventionalCaseNameRector;
use Rector\CodeQuality\Rector\LogicalAnd\LogicalToBooleanRector;
use Rector\CodingStyle\Rector\Assign\SplitDoubleAssignRector;
use Rector\CodingStyle\Rector\ClassLike\NewlineBetweenClassLikeStmtsRector;
use Rector\Config\RectorConfig;
use Rector\DowngradePhp74\Rector\Array_\DowngradeArraySpreadRector;
use Rector\DowngradePhp80\Rector\FuncCall\DowngradeStrContainsRector;
use Rector\DowngradePhp80\Rector\FuncCall\DowngradeStrEndsWithRector;
use Rector\DowngradePhp80\Rector\FuncCall\DowngradeStrStartsWithRector;
use Rector\DowngradePhp81\Rector\FuncCall\DowngradeArrayIsListRector;
use Rector\DowngradePhp82\Rector\MethodCall\DowngradeReflectionMethodHasPrototypeRector;
use Rector\DowngradePhp85\Rector\FuncCall\DowngradeArrayFirstLastRector;
use Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\PostRector\Rector\NameImportingPostRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Set\ValueObject\DowngradeLevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Transform\Rector\String_\StringToClassConstantRector;
use Rector\ValueObject\PhpVersion;

error_reporting(\E_ALL & ~\E_DEPRECATED & ~\E_USER_DEPRECATED);

return RectorConfig::configure()
    ->withPaths([...Utils::defaultRootDirectories(), ...Utils::defaultRootFiles()])
    ->withRootFiles()
    ->withSkip([
        '*/Fixtures/*',
        // __DIR__.'/tests.php',
    ])
    ->withSkip([
        DowngradeArrayFirstLastRector::class,
        DowngradeArrayIsListRector::class,
        DowngradeArraySpreadRector::class,
        DowngradeStrContainsRector::class,
        DowngradeStrEndsWithRector::class,
        DowngradeStrStartsWithRector::class,
    ])
    ->withSkip([
        LogicalToBooleanRector::class,
        NewlineBetweenClassLikeStmtsRector::class,
        PreferPHPUnitThisCallRector::class,
        SplitDoubleAssignRector::class,
    ])
    ->withSkip([
        DowngradeReflectionMethodHasPrototypeRector::class => [
            __DIR__.'/src/Rector/FunctionLike/RenameGarbageParamNameRector.php',
        ],
        NameImportingPostRector::class => [
            __DIR__.'/src/Rector/FunctionLike/RenameGarbageParamNameRector.php',
            __DIR__.'/src/Rector/Name/RenameToConventionalCaseNameRector.php',
            __DIR__.'/src/Rector/New_/NewExceptionToNewAnonymousExtendsExceptionImplementsRector.php',
            __DIR__.'/src/Support/ComposerScripts.php',
            __DIR__.'/src/Support/helpers.php',
            __DIR__.'/tests/Rector/AbstractRectorTestCase.php',
        ],
        RenameClassRector::class => [
            __DIR__.'/config/set/rector.php',
        ],
        RenameParamToMatchTypeRector::class => [
            __DIR__.'/src/Rector/*Rector.php',
        ],
        SortAssociativeArrayByKeyRector::class => [
            /** @see vendor/rector/rector/src/PostRector/Rector/ */
            __DIR__.'/src/',
        ],
        StringToClassConstantRector::class => [
            __DIR__.'/src/Rector/Name/RenameToConventionalCaseNameRector.php',
        ],
    ])
    ->withCache(__DIR__.'/.build/rector/')
    // ->withoutParallel()
    ->withParallel()
    // ->withImportNames(importDocBlockNames: false, importShortClasses: false, removeUnusedImports: false)
    ->withImportNames(true, false, false, false)
    ->reportUnusedSkips()
    ->withFluentCallNewLine()
    ->withTreatClassesAsFinal()
    ->withTypeGuardedClasses([])
    // ->withAttributesSets(phpunit: true, all: true)
    // ->withComposerBased(phpunit: true/* , laravel: true */)
    ->withComposerBased(false, false, true)
    ->withPhpVersion(PhpVersion::PHP_74)
    ->withPhpLevel(70400)
    // ->withDowngradeSets(php74: true)
    // ->withPhpSets(php74: true)
    // ->withPreparedSets(
    //     deadCode: true,
    //     codeQuality: true,
    //     codingStyle: true,
    //     typeDeclarations: true,
    //     typeDeclarationDocblocks: true,
    //     privatization: true,
    //     naming: true,
    //     namedArgs: true,
    //     // carbon: true,
    //     rectorPreset: true,
    //     phpunitCodeQuality: true,
    //     phpunitNarrowAsserts: true,
    //     phpunitMockToStub: true,
    // )
    ->withSets([
        Guanguans\RectorRules\Set\SetList::ALL,
        PHPUnitSetList::ANNOTATIONS_TO_ATTRIBUTES,
        PHPUnitSetList::PHPUNIT_CODE_QUALITY,
        PHPUnitSetList::PHPUNIT_NARROW_ASSERTS,
        PHPUnitSetList::PHPUNIT_MOCK_TO_STUB,
        DowngradeLevelSetList::DOWN_TO_PHP_74,
        SetList::DEAD_CODE,
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::TYPE_DECLARATION,
        SetList::TYPE_DECLARATION_DOCBLOCKS,
        SetList::PRIVATIZATION,
        SetList::NAMING,
        SetList::NAMED_ARGS,
        // SetList::CARBON,
        SetList::RECTOR_PRESET,
        SetList::PHP_POLYFILLS,
    ])
    ->withRules([])
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
    // ->withConfiguredRule(SortListItemOfSameScalarTypeRector::class, [
    //     'ignore_comment' => false,
    //     'ignore_docblock' => false,
    // ])
    ->registerDecoratingNodeVisitor(ParentConnectingVisitor::class)
    ->withConfiguredRule(RenameToConventionalCaseNameRector::class, ['MIT']);
