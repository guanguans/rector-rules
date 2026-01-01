<?php

/** @noinspection LongInheritanceChainInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2025-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/rector-rules
 */

namespace Guanguans\RectorRulesTests\Rector;

use Guanguans\RectorRules\Rector\Name\RenameToPsrNameRector;
use Illuminate\Support\Str;
use PhpCsFixer\FileRemoval;
use PhpParser\NodeVisitor\ParentConnectingVisitor;
use Rector\Config\RegisteredService;
use Rector\Contract\PhpParser\DecoratingNodeVisitorInterface;

abstract class AbstractRectorTestCase extends \Rector\Testing\PHPUnit\AbstractRectorTestCase
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    final public static function setUpBeforeClass(): void
    {
        // if (RenameToPsrNameRector::class !== static::rectorClass()) {
        //     return;
        // }

        $rectorConfig = parent::getContainer();
        $registerService = new RegisteredService(
            ParentConnectingVisitor::class,
            null,
            DecoratingNodeVisitorInterface::class
        );
        $rectorConfig->singleton($registerService->getClassName());
        $rectorConfig->tag($registerService->getClassName(), $registerService->getTag());
    }

    final public function provideConfigFilePath(): string
    {
        return static::directory().'/config/configured_rule.php';
    }

    final public function testRuleDefinition(): void
    {
        $documentedRule = static::rectorReflectionClass()->newInstanceWithoutConstructor();
        $ruleDefinition = $documentedRule->getRuleDefinition();
        self::assertNotEmpty($ruleDefinition->getDescription());
        self::assertNotEmpty($ruleDefinition->getCodeSamples());
        self::assertIsBool($ruleDefinition->isConfigurable());
    }

    /**
     * @dataProvider provideCases()
     *
     * @noinspection PhpUndefinedNamespaceInspection
     * @noinspection PhpUndefinedClassInspection
     * @noinspection PhpLanguageLevelInspection
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provideCases')]
    final public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    final public static function provideCases(): iterable
    {
        return self::yieldFilesFromDirectory(static::directory().'/Fixture');
    }

    /**
     * @noinspection PhpUndefinedClassInspection
     */
    protected function doTestFile(string $fixtureFilePath, bool $includeFixtureDirectoryAsSource = \false): void
    {
        // $reflectionClass = new \ReflectionClass(parent::class);
        // $reflectionMethod = $reflectionClass->getMethod('createInputFilePath');
        // \PHP_VERSION_ID < 80100 and $reflectionMethod->setAccessible(true);
        // $path = $reflectionMethod->invoke($this, $fixtureFilePath);

        // $path = \Closure::bind(
        //     static fn (parent $parent): string => $parent->createInputFilePath($fixtureFilePath),
        //     null,
        //     parent::class
        // )($this);
        // $path = \Closure::bind(fn (): string => $this->createInputFilePath($fixtureFilePath), $this, parent::class)();

        // $path = (static fn (parent $parent): string => $parent->createInputFilePath($fixtureFilePath))->bindTo(
        //     null,
        //     parent::class
        // )($this);
        $path = (fn (): string => $this->createInputFilePath($fixtureFilePath))->bindTo($this, parent::class)();
        \assert(\is_string($path));
        (new FileRemoval)->observe($path);

        parent::doTestFile($fixtureFilePath, $includeFixtureDirectoryAsSource);

        // $arrayOfAbstractRectorTestCase = (array) $this;
        // $key = \sprintf("\0%s\0inputFilePath", parent::class);
        // $key = \sprintf("\x00%s\x00inputFilePath", parent::class);
        // $path = $arrayOfAbstractRectorTestCase[$key];
    }

    /**
     * @throws \ReflectionException
     *
     * @return \ReflectionClass<\Guanguans\RectorRules\Rector\AbstractRector>
     */
    protected static function rectorReflectionClass(): \ReflectionClass
    {
        static $reflectionClass;

        if ($reflectionClass instanceof \ReflectionClass) {
            return $reflectionClass;
        }

        return $reflectionClass = new \ReflectionClass(static::rectorClass());
    }

    /**
     * @return class-string<\Guanguans\RectorRules\Rector\AbstractRector>
     */
    protected static function rectorClass(): string
    {
        static $rectorClass;

        if ($rectorClass) {
            return $rectorClass;
        }

        return $rectorClass = (string) Str::of((new \ReflectionClass(static::class))->getNamespaceName())->replace(
            'RectorRulesTests',
            'RectorRules'
        );
    }

    abstract protected static function directory(): string;
}
