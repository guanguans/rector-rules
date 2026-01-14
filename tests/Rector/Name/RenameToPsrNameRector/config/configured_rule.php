<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpVoidFunctionResultUsedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
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

use Guanguans\RectorRules\Rector\Name\RenameToPsrNameRector;
use PhpParser\NodeVisitor\ParentConnectingVisitor;
use Rector\Config\RectorConfig;
use Rector\Config\RegisteredService;
use Rector\Contract\PhpParser\DecoratingNodeVisitorInterface;

// /**
//  * @see \Rector\PhpDocParser\ValueObject\AttributeKey
//  * @see \Rector\NodeTypeResolver\Node\AttributeKey
//  * @see vendor/rector/rector/src/Configuration/RectorConfigBuilder.php&line=193
//  */
// return RectorConfig::configure()
//     ->registerDecoratingNodeVisitor(ParentConnectingVisitor::class)
//     ->withConfiguredRule(RenameToPsrNameRector::class, [
//     ]);

return static function (RectorConfig $rectorConfig): void {
    // $registeredService = new RegisteredService(
    //     ParentConnectingVisitor::class,
    //     null,
    //     DecoratingNodeVisitorInterface::class
    // );
    // $rectorConfig->singleton($registeredService->getClassName());
    // $rectorConfig->tag($registeredService->getClassName(), $registeredService->getTag());

    // Ensure that using the default configuration is valid.
    $rectorConfig->rule(RenameToPsrNameRector::class);
    $rectorConfig->ruleWithConfiguration(RenameToPsrNameRector::class, [
        'afterAll',
        'afterEach',
        'assertMatches*Snapshot',
        'beforeAll',
        'beforeEach',
        'PDO',
    ]);
};
