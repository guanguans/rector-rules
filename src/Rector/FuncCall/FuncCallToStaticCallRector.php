<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */
declare(strict_types=1);

/**
 * Copyright (c) 2025-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/rector-rules
 */

namespace Guanguans\RectorRules\Rector\FuncCall;

use Guanguans\RectorRules\Rector\AbstractRector;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Transform\ValueObject\FuncCallToStaticCall;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Webmozart\Assert\Assert;

/**
 * @see \Guanguans\RectorRulesTests\Rector\FuncCall\FuncCallToStaticCallRector\FuncCallToStaticCallRectorTest
 */
final class FuncCallToStaticCallRector extends AbstractRector implements ConfigurableRectorInterface
{
    /** @var list<FuncCallToStaticCall> */
    private array $funcCallsToStaticCalls = [];

    /**
     * @return list<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    /**
     * @see https://github.com/rectorphp/rector-src/blob/2.7.0/rules/Transform/Rector/FuncCall/FuncCallToStaticCallRector.php
     *
     * @param \PhpParser\Node\Expr\FuncCall $node
     */
    public function refactor(Node $node): ?Node
    {
        foreach ($this->funcCallsToStaticCalls as $funcCallToStaticCall) {
            if (! $this->isName($node, $funcCallToStaticCall->getOldFuncName())) {
                continue;
            }

            return $this->nodeFactory->createStaticCall(
                $funcCallToStaticCall->getNewClassName(),
                $funcCallToStaticCall->getNewMethodName(),
                $node->args
            );
        }

        return null;
    }

    /**
     * @param list<mixed> $configuration
     */
    public function configure(array $configuration): void
    {
        Assert::allIsAOf($configuration, FuncCallToStaticCall::class);

        $this->funcCallsToStaticCalls = $configuration;
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
                'view("...", []);',
                'SomeClass::render("...", []);',
                [new FuncCallToStaticCall('view', 'SomeStaticClass', 'render')]
            ),
        ];
    }
}
