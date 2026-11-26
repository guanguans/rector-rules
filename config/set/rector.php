<?php

/** @noinspection PhpInternalEntityUsedInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUndefinedNamespaceInspection */
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

use Guanguans\RectorRules\Rector\Array_\UpdateRectorCodeSamplesFromFixturesRector;
use Guanguans\RectorRules\Rector\Class_\UpdateRectorMethodNodeParamDocblockFromNodeTypesRector;
use Guanguans\RectorRules\Rector\Scalar\ScalarValueToConstFetchRector;
use Guanguans\RectorRules\Rector\String_\StringToClassConstantRector;
use Guanguans\RectorRules\Support\Utils;
use PhpParser\NodeFinder;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\Reflection\BetterReflection\BetterReflectionSourceLocatorFactory;
use Rector\BetterPhpDocParser\PhpDocParser\BetterPhpDocParser;
use Rector\BetterPhpDocParser\ValueObject\PhpDocAttributeKey;
use Rector\Config\RectorConfig;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\NodeTypeResolver\Reflection\BetterReflection\RectorBetterReflectionSourceLocatorFactory;
use Rector\PhpParser\Node\BetterNodeFinder;
use Rector\PhpParser\Parser\InlineCodeParser;
use Rector\PhpParser\Parser\RectorParser;
use Rector\PhpParser\Parser\SimplePhpParser;
use Rector\PhpParser\Printer\BetterStandardPrinter;
use Rector\Rector\AbstractRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\ValueObject\PhpVersion;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__.'/../config.php');

    /**
     * @see https://packagist.org/packages/rector/rector
     */
    if (!class_exists(AbstractRector::class)) {
        return;
    }

    // $rectorConfig->skip([__FILE__]);
    $rectorConfig->rules([
        UpdateRectorCodeSamplesFromFixturesRector::class,
        UpdateRectorMethodNodeParamDocblockFromNodeTypesRector::class,
    ]);

    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        BetterReflectionSourceLocatorFactory::class => RectorBetterReflectionSourceLocatorFactory::class,
        NodeFinder::class => BetterNodeFinder::class,
        PhpDocParser::class => BetterPhpDocParser::class,
        // InlineCodeParser::class => RectorParser::class,
        // SimplePhpParser::class => RectorParser::class,
        Standard::class => BetterStandardPrinter::class,
    ]);

    $rectorConfig->ruleWithConfiguration(
        StringToClassConstantRector::class,
        Utils::configurationOfStringToClassConstantRector([AttributeKey::class, PhpDocAttributeKey::class])
    );

    $rectorConfig->ruleWithConfiguration(
        ScalarValueToConstFetchRector::class,
        Utils::configurationOfScalarValueToConstFetchRector(PhpVersion::class)
    );
};
