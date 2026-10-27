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

namespace Guanguans\RectorRules\Rector\StaticCall;

use Guanguans\RectorRules\Rector\AbstractRector;
use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Renaming\ValueObject\RenameStaticMethod;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Webmozart\Assert\Assert;

/**
 * @see \Guanguans\RectorRulesTests\Rector\StaticCall\RenameStaticMethodRector\RenameStaticMethodRectorTest
 */
final class RenameStaticMethodRector extends AbstractRector implements ConfigurableRectorInterface
{
    /** @var list<RenameStaticMethod> */
    private array $staticMethodRenames = [];

    /**
     * @return list<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [StaticCall::class];
    }

    /**
     * @see https://github.com/rectorphp/rector-src/blob/2.7.0/rules/Renaming/Rector/StaticCall/RenameStaticMethodRector.php
     *
     * @param \PhpParser\Node\Expr\StaticCall $node
     */
    public function refactor(Node $node): ?Node
    {
        foreach ($this->staticMethodRenames as $staticMethodRename) {
            if (! $this->isName($node->name, $staticMethodRename->getOldMethod())) {
                continue;
            }

            if (! $this->isObjectType($node->class, $staticMethodRename->getOldObjectType())) {
                continue;
            }

            return $this->rename($node, $staticMethodRename);
        }

        return null;
    }

    /**
     * @param list<mixed> $configuration
     */
    public function configure(array $configuration): void
    {
        Assert::allIsAOf($configuration, RenameStaticMethod::class);
        $this->staticMethodRenames = $configuration;
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
                <<<'EOD'
                    /** @noinspection ALL */
                    namespace Guanguans\RectorRulesTests\Rector\StaticCall\RenameStaticMethodRector\Fixture;

                    function staticMethodNameReplacer()
                    {

                    EOD.\Nette\Utils\Html::class.<<<'EOD'
                    ::add('someContent');
                        $result = FormMacros::renderFormBegin($someArgs);
                        $result = FormMACROS::renderFormBEGIN($someArgs);
                    }
                    EOD,
                <<<'EOD'
                    /** @noinspection ALL */
                    namespace Guanguans\RectorRulesTests\Rector\StaticCall\RenameStaticMethodRector\Fixture;

                    function staticMethodNameReplacer()
                    {

                    EOD.\Nette\Utils\Html::class.<<<'EOD'
                    ::addHtml('someContent');
                        $result = FormMacros::renderFormBegin($someArgs);
                        $result = FormMACROS::renderFormBEGIN($someArgs);
                    }
                    EOD,
                [
                    new RenameStaticMethod(\Nette\Utils\Html::class, 'add', \Nette\Utils\Html::class, 'addHtml'),
                    new RenameStaticMethod(\Guanguans\RectorRulesTests\Rector\StaticCall\RenameStaticMethodRector\Source\FormMacros::class, 'renderFormBegin', 'Nette\Bridges\FormsLatte\Runtime', 'renderFormBegin'),
                ]
            ),
        ];
    }

    private function rename(StaticCall $staticCall, RenameStaticMethod $renameStaticMethod): StaticCall
    {
        $staticCall->name = new Identifier($renameStaticMethod->getNewMethod());

        if ($renameStaticMethod->hasClassChanged()) {
            $staticCall->class = new FullyQualified($renameStaticMethod->getNewClass());
        }

        return $staticCall;
    }
}
