<?php

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
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\MutatingScope;
use PHPStan\Reflection\ClassReflection;
use Rector\Config\RectorConfig;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Testing\Fixture\FixtureSplitter;

/**
 * @internal
 */
final class UpdateRectorCodeSamplesRector extends AbstractRector
{
    private RectorConfig $rectorConfig;

    public function __construct(RectorConfig $rectorConfig)
    {
        $this->rectorConfig = $rectorConfig;
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
     * @throws \ReflectionException
     *
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     * @noinspection NotOptimalIfConditionsInspection
     */
    public function refactor(Node $node): ?Node
    {
        $scope = $node->getAttribute('scope');
        \assert($scope instanceof MutatingScope);

        if (
            [] === $node->items
            || !\in_array($scope->getFunctionName(), ['getRuleDefinition', 'codeSamples'], true)
            || !($classReflection = $scope->getClassReflection()) instanceof ClassReflection
            || !$classReflection->is(AbstractRector::class)
            || !$classReflection->getNativeReflection()->isInstantiable()
        ) {
            return null;
        }

        $configuration = $this->configurationFor($classReflection);
        null !== $configuration and $configurationNode = $this->nodeFactory->createArray($configuration);

        $fixtureFiles = glob(str_replace(
            ['/src/', '.php'],
            ['/tests/', '/Fixture/fixture*.php.inc'],
            $classReflection->getNativeReflection()->getFileName()
        ));

        $hasChanged = false;

        foreach ($fixtureFiles as $index => $fixtureFile) {
            [$badCode, $goodCode] = array_map(
                static fn (string $code): string => (string) Str::of($code)
                    ->trim()
                    ->whenStartsWith($start = '<?php', static fn (Stringable $code) => $code->replaceFirst($start, ''))
                    ->whenEndsWith($finish = '?>', static fn (Stringable $code) => $code->replaceLast($finish, ''))
                    ->trim(),
                FixtureSplitter::split($fixtureFile)
            );

            $arrayItem = $node->items[$index] ?? clone $node->items[0];

            if ($arrayItem->value->args[0]->value->value !== $badCode) {
                $arrayItem->value->args[0]->value->value = $badCode;

                if ($arrayItem->value->args[0]->value instanceof String_) {
                    $arrayItem->value->args[0]->value->setAttribute('docLabel', 'PHP');
                }

                $hasChanged = true;
            }

            if ($arrayItem->value->args[1]->value->value !== $goodCode) {
                $arrayItem->value->args[1]->value->value = $goodCode;

                if ($arrayItem->value->args[1]->value instanceof String_) {
                    $arrayItem->value->args[1]->value->setAttribute('docLabel', 'PHP');
                }

                $hasChanged = true;
            }

            if (
                isset($configurationNode)
                && !$this->nodeComparator->areNodesEqual($arrayItem->value->args[2]->value, $configurationNode)
            ) {
                $arrayItem->value->args[2]->value = $configurationNode;
                $hasChanged = true;
            }
        }

        return $hasChanged ? $node : null;
    }

    /**
     * @return list<\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample>
     */
    protected function codeSamples(): array
    {
        return [
        ];
    }

    /**
     * @throws \Rector\Exception\ShouldNotHappenException
     *
     * @return null|array<array-key, mixed>
     */
    private function configurationFor(ClassReflection $classReflection): ?array
    {
        if (!$classReflection->getNativeReflection()->isSubclassOf(ConfigurableRectorInterface::class)) {
            return null;
        }

        $configFile = str_replace(
            ['/src/', '.php'],
            ['/tests/', '/config/configured_rule.php'],
            $classReflection->getNativeReflection()->getFileName()
        );

        if (!is_file($configFile)) {
            return null;
        }

        $this->rectorConfig->import($configFile);

        return $this->rectorConfig->getRuleConfigurations()[$classReflection->getNativeReflection()->getName()];
    }
}
