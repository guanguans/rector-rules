<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */
/** @noinspection EfferentObjectCouplingInspection */
declare(strict_types=1);

/**
 * Copyright (c) 2025-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/rector-rules
 */

namespace Guanguans\RectorRules\Rector\Array_;

use Guanguans\RectorRules\Rector\AbstractRector;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\MutatingScope;
use PHPStan\Reflection\ClassReflection;
use Rector\Config\RectorConfig;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Testing\Fixture\FixtureSplitter;
use Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Webmozart\Assert\Assert;
use function Guanguans\RectorRules\Support\clone_node;

/**
 * @internal
 */
final class UpdateRectorCodeSamplesFromFixturesRector extends AbstractRector
{
    private RectorConfig $rectorConfig;

    public function __construct(RectorConfig $rectorConfig)
    {
        $this->rectorConfig = $rectorConfig;
        // $this->rectorConfig = clone $rectorConfig;
        // $this->rectorConfig = unserialize(serialize($rectorConfig), ['allowed_classes' => true]);
    }

    public function getNodeTypes(): array
    {
        return [
            Array_::class,
        ];
    }

    /**
     * @param \PhpParser\Node\Expr\Array_ $node
     *
     * @throws \Rector\Exception\ShouldNotHappenException
     *
     * @noinspection NotOptimalIfConditionsInspection
     */
    public function refactor(Node $node): ?Node
    {
        $scope = $node->getAttribute('scope');
        \assert($scope instanceof MutatingScope);

        if (
            [] === $node->items
            || !collect($node->items)->every(
                fn (ArrayItem $arrayItem): bool => $arrayItem->value instanceof New_
                    && $arrayItem->value->class instanceof FullyQualified
                    && is_subclass_of($this->getName($arrayItem->value->class), CodeSampleInterface::class)
            )
            || !\in_array($scope->getFunctionName(), ['getRuleDefinition', 'codeSamples'], true)
            || !($classReflection = $scope->getClassReflection()) instanceof ClassReflection
            || !$classReflection->is(AbstractRector::class)
            || !$classReflection->getNativeReflection()->isInstantiable()
        ) {
            return null;
        }

        return $this->doRefactor($node, $classReflection) ? $node : null;
    }

    /**
     * @return list<\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample>
     */
    protected function codeSamples(): array
    {
        return [
            new CodeSample(
                <<<'PHP_WRAP'
                    /** @noinspection ALL */
                    namespace Guanguans\RectorRules\Rector\Array_;

                    use Guanguans\RectorRules\Rector\AbstractRector;
                    use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;

                    final class SimplifyListIndexRector extends AbstractRector
                    {
                        protected function codeSamples(): array
                        {
                            return [
                                new CodeSample(
                                    <<<'PHP'
                                    /** @noinspection ALL */
                                    [0 => 'foo', 1 => 'bar', 2 => 'baz'];
                                    // [0 => 'foo', 'bar', 2 => 'baz'];
                                    PHP,
                                    <<<'PHP'
                                    /** @noinspection ALL */
                                    ['foo', 'bar', 'baz'];
                                    // ['foo', 'bar', 'baz'];
                                    PHP,
                                ),
                            ];
                        }
                    }
                    PHP_WRAP,
                <<<'PHP_WRAP'
                    /** @noinspection ALL */
                    namespace Guanguans\RectorRules\Rector\Array_;

                    use Guanguans\RectorRules\Rector\AbstractRector;
                    use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;

                    final class SimplifyListIndexRector extends AbstractRector
                    {
                        protected function codeSamples(): array
                        {
                            return [
                                new CodeSample(
                                    <<<'PHP'
                                    /** @noinspection ALL */
                                    [0 => 'foo', 1 => 'bar', 2 => 'baz'];
                                    [0 => 'foo', 'bar', 2 => 'baz'];
                                    PHP,
                                    <<<'PHP'
                                    /** @noinspection ALL */
                                    ['foo', 'bar', 'baz'];
                                    ['foo', 'bar', 'baz'];
                                    PHP,
                                ),
                            ];
                        }
                    }
                    PHP_WRAP,
            ), new CodeSample(
                <<<'PHP_WRAP'
                    /** @noinspection ALL */
                    namespace Guanguans\RectorRules\Rector\New_;

                    use Guanguans\RectorRules\Rector\AbstractRector;
                    use Rector\Contract\Rector\ConfigurableRectorInterface;
                    use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;

                    final class NewExceptionToNewAnonymousExtendsExceptionImplementsRector extends AbstractRector implements ConfigurableRectorInterface
                    {
                        protected function codeSamples(): array
                        {
                            return [
                                new ConfiguredCodeSample(
                                    <<<'PHP'
                                    /** @noinspection ALL */
                                    // new Exception('Testing');
                                    PHP,
                                    <<<'PHP'
                                    /** @noinspection ALL */
                                    // new class('Testing') extends \Exception implements \Guanguans\RectorRules\Contract\ThrowableContract
                                    // {
                                    // };
                                    PHP,
                                    [/*'Guanguans\RectorRules\Contract\ThrowableContract'*/],
                                ),
                            ];
                        }
                    }
                    PHP_WRAP,
                <<<'PHP_WRAP'
                    /** @noinspection ALL */
                    namespace Guanguans\RectorRules\Rector\New_;

                    use Guanguans\RectorRules\Rector\AbstractRector;
                    use Rector\Contract\Rector\ConfigurableRectorInterface;
                    use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;

                    final class NewExceptionToNewAnonymousExtendsExceptionImplementsRector extends AbstractRector implements ConfigurableRectorInterface
                    {
                        protected function codeSamples(): array
                        {
                            return [
                                new ConfiguredCodeSample(
                                    <<<'PHP'
                                    /** @noinspection ALL */
                                    new Exception('Testing');
                                    PHP,
                                    <<<'PHP'
                                    /** @noinspection ALL */
                                    new class('Testing') extends \Exception implements \Guanguans\RectorRules\Contract\ThrowableContract
                                    {
                                    };
                                    PHP,
                                    ['Guanguans\RectorRules\Contract\ThrowableContract'],
                                ),
                            ];
                        }
                    }
                    PHP_WRAP,
            ),
        ];
    }

    /**
     * @throws \Rector\Exception\ShouldNotHappenException
     */
    private function doRefactor(Array_ $node, ClassReflection $classReflection): bool
    {
        $configurationNode = $this->configurationNodeFor($classReflection);
        $fixtureFiles = $this->fixtureFilesFor($classReflection);
        $hasChanged = false;

        foreach ($fixtureFiles as $index => $fixtureFile) {
            // $arrayItem = $node->items[$index] ?? clone $node->items[0];
            $arrayItem = $node->items[$index] ?? clone_node($node->items[0]);
            \assert($arrayItem->value instanceof New_);
            Assert::allIsInstanceOf($arrayItem->value->args, Arg::class);
            [$badCode, $goodCode] = array_map([$this, 'sanitizeCode'], FixtureSplitter::split($fixtureFile));

            $badCodeNode = $arrayItem->value->args[0]->value;
            \assert($badCodeNode instanceof String_);

            if ($badCodeNode->value !== $badCode) {
                $badCodeNode->value = $badCode;
                $badCodeNode->setAttribute('docLabel', 'PHP');
                $hasChanged = true;
            }

            $goodCodeNode = $arrayItem->value->args[1]->value;
            \assert($goodCodeNode instanceof String_);

            if ($goodCodeNode->value !== $goodCode) {
                $goodCodeNode->value = $goodCode;
                $goodCodeNode->setAttribute('docLabel', 'PHP');
                $hasChanged = true;
            }

            if (
                $configurationNode instanceof Array_
                && !$this->nodeComparator->areNodesEqual($arrayItem->value->args[2]->value, $configurationNode)
            ) {
                $arrayItem->value->args[2]->value = $configurationNode;
                $hasChanged = true;
            }
        }

        return $hasChanged;
    }

    /**
     * @throws \Rector\Exception\ShouldNotHappenException
     */
    private function configurationNodeFor(ClassReflection $classReflection): ?Array_
    {
        if (!$classReflection->getNativeReflection()->isSubclassOf(ConfigurableRectorInterface::class)) {
            return null;
        }

        $configFile = $this->configFileFor($classReflection);

        if (!is_file($configFile)) {
            return null;
        }

        /** @var list<string> $importedConfigFiles */
        static $importedConfigFiles = [];

        if (!\in_array($configFile, $importedConfigFiles, true)) {
            $this->rectorConfig->import($configFile);
            $importedConfigFiles[] = $configFile;
        }

        $ruleConfigurations = $this->rectorConfig->getRuleConfigurations();
        $class = $classReflection->getNativeReflection()->getName();
        \assert(\is_string($class));

        // if (!isset($ruleConfigurations[$class])) {
        //     return null;
        // }

        return $this->nodeFactory->createArray($ruleConfigurations[$class]);
    }

    private function configFileFor(ClassReflection $classReflection): string
    {
        // return str_replace(
        //     ['/src/', '.php'],
        //     ['/tests/', '/config/configured_rule.php'],
        //     $classReflection->getNativeReflection()->getFileName()
        // );
        return (string) Str::of($classReflection->getNativeReflection()->getName())
            ->replace(['Guanguans\\RectorRules\\', '\\'], ['tests/', '/'])
            ->append('/config/configured_rule.php');
    }

    /**
     * @return list<string>
     */
    private function fixtureFilesFor(ClassReflection $classReflection): array
    {
        // $fixtureFiles = glob(str_replace(
        //     ['/src/', '.php'],
        //     ['/tests/', '/Fixture/fixture*.php.inc'],
        //     $classReflection->getNativeReflection()->getFileName()
        // ));
        return glob(
            (string) Str::of($classReflection->getNativeReflection()->getName())
                ->replace(['Guanguans\\RectorRules\\', '\\'], ['tests/', '/'])
                ->append('/Fixture/fixture*.php.inc')
        );
    }

    private function sanitizeCode(string $code): string
    {
        return (string) Str::of($code)
            ->trim()
            ->whenStartsWith($start = '<?php', static fn (Stringable $code) => $code->replaceFirst($start, ''))
            ->whenEndsWith($finish = '?>', static fn (Stringable $code) => $code->replaceLast($finish, ''))
            // ->replaceFirst('/** @noinspection ALL */'.\PHP_EOL, '')
            ->trim();
    }
}
