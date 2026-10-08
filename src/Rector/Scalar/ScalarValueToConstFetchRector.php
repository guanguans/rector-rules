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

namespace Guanguans\RectorRules\Rector\Scalar;

use Guanguans\RectorRules\Rector\AbstractRector;
use Guanguans\RectorRulesTests\Rector\Scalar\ScalarValueToConstFetchRector\Source\ClassWithConst;
use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\Float_;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Scalar\String_;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Transform\ValueObject\ScalarValueToConstFetch;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Webmozart\Assert\Assert;

/**
 * @see \Guanguans\RectorRulesTests\Rector\Scalar\ScalarValueToConstFetchRector\ScalarValueToConstFetchRectorTest
 */
final class ScalarValueToConstFetchRector extends AbstractRector implements ConfigurableRectorInterface
{
    /** @var list<ScalarValueToConstFetch> */
    private array $scalarValueToConstFetches;

    public function getNodeTypes(): array
    {
        return [String_::class, Float_::class, Int_::class];
    }

    /**
     * @see https://github.com/rectorphp/rector-src/blob/bc675f031ba86784696adb08f416548c9d4dc406/rules/Transform/Rector/Scalar/ScalarValueToConstFetchRector.php
     *
     * @param \PhpParser\Node\Scalar\Float_|\PhpParser\Node\Scalar\Int_|\PhpParser\Node\Scalar\String_ $node
     *
     * @return null|\PhpParser\Node\Expr\ClassConstFetch|\PhpParser\Node\Expr\ConstFetch
     */
    public function refactor(Node $node): ?Node
    {
        foreach ($this->scalarValueToConstFetches as $scalarValueToConstFetch) {
            if ($scalarValueToConstFetch->getScalar()->value === $node->value) {
                return $scalarValueToConstFetch->getConstFetch();
            }
        }

        return null;
    }

    public function configure(array $configuration): void
    {
        Assert::isList($configuration);
        Assert::allIsAOf($configuration, ScalarValueToConstFetch::class);

        $this->scalarValueToConstFetches = $configuration;
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
                <<<'PHP'
                    /** @noinspection ALL */
                    namespace Guanguans\RectorRulesTests\Rector\Scalar\ScalarValueToConstFetchRector\Source\Fixture;

                    $int = 10;
                    $float = 10.1;
                    $string = 'ABC';
                    PHP,
                <<<'PHP'
                    /** @noinspection ALL */
                    namespace Guanguans\RectorRulesTests\Rector\Scalar\ScalarValueToConstFetchRector\Source\Fixture;

                    $int = \Guanguans\RectorRulesTests\Rector\Scalar\ScalarValueToConstFetchRector\Source\ClassWithConst::FOOBAR_INT;
                    $float = \Guanguans\RectorRulesTests\Rector\Scalar\ScalarValueToConstFetchRector\Source\ClassWithConst::FOOBAR_FLOAT;
                    $string = \Guanguans\RectorRulesTests\Rector\Scalar\ScalarValueToConstFetchRector\Source\ClassWithConst::FOOBAR_STRING;
                    PHP,
                [
                    new ScalarValueToConstFetch(
                        new Int_(10),
                        new ClassConstFetch(new FullyQualified(ClassWithConst::class), new Identifier('FOOBAR_INT'))
                    ),
                    new ScalarValueToConstFetch(
                        new Float_(10.1),
                        new ClassConstFetch(new FullyQualified(ClassWithConst::class), new Identifier('FOOBAR_FLOAT'))
                    ),
                    new ScalarValueToConstFetch(
                        new String_('ABC'),
                        new ClassConstFetch(new FullyQualified(ClassWithConst::class), new Identifier('FOOBAR_STRING'))
                    ),
                ]
            ),
        ];
    }
}
