<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpVoidFunctionResultUsedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection LongInheritanceChainInspection */
/** @noinspection PhpInternalEntityUsedInspection */
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

namespace Guanguans\RectorRulesTests\Rector;

use Guanguans\RectorRules\NodeVisitor\ParentConnectingVisitor;
use Guanguans\RectorRules\Rector\Name\RenameToConventionalCaseNameRector;
use Illuminate\Support\Str;
use PhpCsFixer\FileRemoval;

abstract class AbstractRectorTestCase extends \Rector\Testing\PHPUnit\AbstractRectorTestCase
{
    /**
     * @see https://getrector.com/documentation/creating-node-visitor
     * @see \Rector\DependencyInjection\LazyContainerFactory::registerNodeVisitorsAndPhpDoc()
     * @see \Rector\Configuration\RectorConfigBuilder::registerDecoratingNodeVisitor()
     * @see \Rector\Configuration\RectorConfigBuilder::registerService()
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    final public static function setUpBeforeClass(): void
    {
        // if (RenameToConventionalCaseNameRector::class !== static::rectorClass()) {
        //     return;
        // }

        $rectorConfig = parent::getContainer();
        $rectorConfig->singleton(ParentConnectingVisitor::class);
    }

    final public function provideConfigFilePath(): string
    {
        return static::directory().'/config/configured_rule.php';
    }

    final public function testRectorTestCaseClassName(): void
    {
        self::assertSame(
            static::class,
            (string) Str::of(static::rectorClass())
                ->replace('RectorRules', 'RectorRulesTests')
                ->append(
                    '\\',
                    static::rectorReflectionClass()
                        ->getShortName(),
                    'Test'
                )
        );
    }

    final public function testRuleDefinition(): void
    {
        $ruleDefinition = static::rectorReflectionClass()->newInstanceWithoutConstructor()->getRuleDefinition();

        self::assertNotEmpty($ruleDefinition->getDescription());
        self::assertNotEmpty($ruleDefinition->getCodeSamples());
        self::assertIsBool($ruleDefinition->isConfigurable());
    }

    /**
     * @dataProvider provideCases()
     *
     * @noinspection PhpFullyQualifiedNameUsageInspection
     * @noinspection PhpLanguageLevelInspection
     * @noinspection PhpUndefinedNamespaceInspection
     *
     * @doesNotPerformAssertions
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provideCases')]
    final public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    final public static function provideCases(): iterable
    {
        yield from self::yieldFilesFromDirectory(static::directory().'/Fixture/');

        foreach ((array) glob(static::directory().'/Fixture\d*/', \GLOB_ONLYDIR) as $directory) {
            if ((int) (\PHP_MAJOR_VERSION.\PHP_MINOR_VERSION) >= (int) (string) Str::of($directory)->basename()->substr(7)) {
                yield from self::yieldFilesFromDirectory($directory);
            }
        }
    }

    abstract protected static function directory(): string;

    protected function doTestFile(string $fixtureFilePath, bool $includeFixtureDirectoryAsSource = false): void
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
        return new \ReflectionClass(static::rectorClass());
    }

    /**
     * @return class-string<\Guanguans\RectorRules\Rector\AbstractRector>
     */
    protected static function rectorClass(): string
    {
        return (string) Str::of((new \ReflectionClass(static::class))->getNamespaceName())->replace(
            'RectorRulesTests',
            'RectorRules'
        );
    }
}
