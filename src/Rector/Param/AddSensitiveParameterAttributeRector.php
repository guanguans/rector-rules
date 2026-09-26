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

namespace Guanguans\RectorRules\Rector\Param;

use Guanguans\RectorRules\Rector\AbstractRector;
use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Php80\NodeAnalyzer\PhpAttributeAnalyzer;
use Rector\ValueObject\PhpVersionFeature;
use Rector\VersionBonding\Contract\MinPhpVersionInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Webmozart\Assert\Assert;

/**
 * @see \Guanguans\RectorRulesTests\Rector\Param\AddSensitiveParameterAttributeRector\AddSensitiveParameterAttributeRectorTest
 */
final class AddSensitiveParameterAttributeRector extends AbstractRector implements ConfigurableRectorInterface, MinPhpVersionInterface
{
    /** @api */
    public const SENSITIVE_PARAMETERS = 'sensitive_parameters';

    /** @readonly */
    private PhpAttributeAnalyzer $phpAttributeAnalyzer;

    /** @var list<string> */
    private array $sensitiveParameters = [];

    public function __construct(PhpAttributeAnalyzer $phpAttributeAnalyzer)
    {
        $this->phpAttributeAnalyzer = $phpAttributeAnalyzer;
    }

    public function getNodeTypes(): array
    {
        return [Param::class];
    }

    /**
     * @see https://github.com/rectorphp/rector-src/blob/bc675f031ba86784696adb08f416548c9d4dc406/rules/Php82/Rector/Param/AddSensitiveParameterAttributeRector.php
     *
     * @param \PhpParser\Node\Param $node
     */
    public function refactor(Node $node): ?Param
    {
        if (
            !$this->isNames($node, $this->sensitiveParameters)
            || $this->phpAttributeAnalyzer->hasPhpAttribute($node, 'SensitiveParameter')
        ) {
            return null;
        }

        $node->attrGroups[] = new AttributeGroup([new Attribute(new FullyQualified('SensitiveParameter'))]);

        return $node;
    }

    public function provideMinPhpVersion(): int
    {
        return PhpVersionFeature::SENSITIVE_PARAMETER_ATTRIBUTE;
    }

    /**
     * @param array<string, mixed> $configuration
     */
    public function configure(array $configuration): void
    {
        $sensitiveParameters = $configuration[self::SENSITIVE_PARAMETERS] ?? [];
        Assert::isList($sensitiveParameters);
        Assert::allString($sensitiveParameters);
        $this->sensitiveParameters = $sensitiveParameters;
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
                    namespace Guanguans\RectorRulesTests\Rector\Param\AddSensitiveParameterAttributeRector\Fixture;

                    function login($username, $password): void
                    {
                    }

                    class ApplyAttributeToMethods
                    {
                        public function login($username, $password): void
                        {
                        }
                    }
                    PHP,
                <<<'PHP'
                    /** @noinspection ALL */
                    namespace Guanguans\RectorRulesTests\Rector\Param\AddSensitiveParameterAttributeRector\Fixture;

                    function login($username, #[\SensitiveParameter]
                    $password): void
                    {
                    }

                    class ApplyAttributeToMethods
                    {
                        public function login($username, #[\SensitiveParameter]
                        $password): void
                        {
                        }
                    }
                    PHP,
                [self::SENSITIVE_PARAMETERS => ['password']]
            ),
        ];
    }
}
