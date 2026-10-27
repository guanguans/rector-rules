<?php

declare(strict_types=1);

/**
 * Copyright (c) 2025-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/rector-rules
 */

namespace Guanguans\RectorRules\Rector\String_;

use Guanguans\RectorRules\Rector\AbstractRector;
use PhpParser\Node;
use PhpParser\Node\Scalar\String_;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\PhpParser\Node\Value\ValueResolver;
use Rector\Transform\ValueObject\StringToClassConstant;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Webmozart\Assert\Assert;

/**
 * @see \Guanguans\RectorRulesTests\Rector\String_\StringToClassConstantRector\StringToClassConstantRectorTest
 */
final class StringToClassConstantRector extends AbstractRector implements ConfigurableRectorInterface
{
    /** @var list<StringToClassConstant> */
    private array $stringsToClassConstants = [];

    /** @readonly */
    private ValueResolver $valueResolver;

    public function __construct(ValueResolver $valueResolver)
    {
        $this->valueResolver = $valueResolver;
    }

    /**
     * @return list<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [String_::class];
    }

    /**
     * @see https://github.com/rectorphp/rector-src/blob/2.7.0/rules/Transform/Rector/String_/StringToClassConstantRector.php
     *
     * @param \PhpParser\Node\Scalar\String_ $node
     */
    public function refactor(Node $node): ?Node
    {
        foreach ($this->stringsToClassConstants as $stringToClassConstant) {
            if (! $this->valueResolver->isValue($node, $stringToClassConstant->getString())) {
                continue;
            }

            return $this->nodeFactory->createClassConstFetch(
                $stringToClassConstant->getClass(),
                $stringToClassConstant->getConstant()
            );
        }

        return null;
    }

    /**
     * @param list<mixed> $configuration
     */
    public function configure(array $configuration): void
    {
        Assert::allIsAOf($configuration, StringToClassConstant::class);

        $this->stringsToClassConstants = $configuration;
    }

    /**
     * @throws \Symplify\RuleDocGenerator\Exception\ShouldNotHappenException
     *
     * @return list<\Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample>
     */
    protected function codeSamples(): array
    {
        return [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
                    final class SomeSubscriber
                    {
                        public static function getSubscribedEvents()
                        {
                            return ['compiler.post_dump' => 'compile'];
                        }
                    }
                    CODE_SAMPLE,
                <<<'CODE_SAMPLE'
                    final class SomeSubscriber
                    {
                        public static function getSubscribedEvents()
                        {
                            return [\Yet\AnotherClass::CONSTANT => 'compile'];
                        }
                    }
                    CODE_SAMPLE,
                [new StringToClassConstant('compiler.post_dump', 'Yet\AnotherClass', 'CONSTANT')]
            ),
        ];
    }
}
