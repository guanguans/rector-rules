<?php

/** @noinspection PhpUnusedAliasInspection */
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

use Guanguans\RectorRules\Rector\Array_\UpdateRectorCodeSamplesFromFixturesRector;
use Guanguans\RectorRules\Rector\Class_\UpdateRectorMethodNodeParamDocblockFromNodeTypesRector;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\Int_;
use PhpParser\NodeFinder;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\Reflection\BetterReflection\BetterReflectionSourceLocatorFactory;
use Rector\BetterPhpDocParser\PhpDocParser\BetterPhpDocParser;
use Rector\BetterPhpDocParser\ValueObject\Parser\BetterTokenIterator;
use Rector\BetterPhpDocParser\ValueObject\PhpDocAttributeKey;
use Rector\Config\RectorConfig;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\NodeTypeResolver\Reflection\BetterReflection\RectorBetterReflectionSourceLocatorFactory;
use Rector\PhpParser\Node\BetterNodeFinder;
use Rector\PhpParser\Parser\RectorParser;
use Rector\PhpParser\Parser\SimplePhpParser;
use Rector\PhpParser\Printer\BetterStandardPrinter;
use Rector\Rector\AbstractRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Transform\Rector\Scalar\ScalarValueToConstFetchRector;
use Rector\Transform\Rector\String_\StringToClassConstantRector;
use Rector\Transform\ValueObject\ScalarValueToConstFetch;
use Rector\Transform\ValueObject\StringToClassConstant;
use Rector\ValueObject\PhpVersion;

return static function (RectorConfig $rectorConfig): void {
    if (!class_exists(AbstractRector::class)) {
        return;
    }

    $rectorConfig->import(__DIR__.'/../config.php');
    $rectorConfig->skip([__FILE__]);
    $rectorConfig->rules([
        UpdateRectorCodeSamplesFromFixturesRector::class,
        UpdateRectorMethodNodeParamDocblockFromNodeTypesRector::class,
    ]);

    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        BetterReflectionSourceLocatorFactory::class => RectorBetterReflectionSourceLocatorFactory::class,
        NodeFinder::class => BetterNodeFinder::class,
        PhpDocParser::class => BetterPhpDocParser::class,
        // SimplePhpParser::class => RectorParser::class,
        Standard::class => BetterStandardPrinter::class,
        TokenIterator::class => BetterTokenIterator::class,
    ]);

    $rectorConfig->ruleWithConfiguration(StringToClassConstantRector::class, array_reduce(
        [
            AttributeKey::class,
            PhpDocAttributeKey::class,
        ],
        static fn (array $carry, string $class): array => array_merge(
            $carry,
            array_map(
                static fn (
                    string $string,
                    string $constant
                ): StringToClassConstant => new StringToClassConstant($string, $class, $constant),
                $constants = (new ReflectionClass($class))->getConstants(),
                array_keys($constants),
            ),
        ),
        [],
    ));

    $rectorConfig->ruleWithConfiguration(
        ScalarValueToConstFetchRector::class,
        collect((new ReflectionClass(PhpVersion::class))->getConstants())
            ->map(static fn (int $value, string $name): ScalarValueToConstFetch => new ScalarValueToConstFetch(
                new Int_($value),
                new ClassConstFetch(new FullyQualified(PhpVersion::class), new Identifier($name))
            ))
            ->all()
    );
};
