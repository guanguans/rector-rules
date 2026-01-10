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

namespace Guanguans\RectorRules\Rector\New_;

use Guanguans\RectorRules\Contract\ThrowableContract;
use Guanguans\RectorRules\Rector\AbstractRector;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\ValueObject\PhpVersion;
use Rector\VersionBonding\Contract\MinPhpVersionInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Webmozart\Assert\Assert;
use function Guanguans\RectorRules\Support\is_subclass_of_all;

/**
 * @see \Guanguans\RectorRulesTests\Rector\New_\NewExceptionToNewAnonymousExtendsExceptionImplementsRector\NewExceptionToNewAnonymousExtendsExceptionImplementsRectorTest
 */
final class NewExceptionToNewAnonymousExtendsExceptionImplementsRector extends AbstractRector implements ConfigurableRectorInterface, MinPhpVersionInterface
{
    /** @var list<class-string> */
    private array $implements = [];

    public function getNodeTypes(): array
    {
        return [
            New_::class,
        ];
    }

    /**
     * @see https://github.com/symfony/ai/blob/main/.phpstan/ForbidNativeExceptionRule.php
     * @see \PhpParser\NodeVisitor::*
     * @see \Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory
     * @see \Rector\Comments\NodeDocBlock\DocBlockUpdater
     * @see \RectorPrefix202512\dump_node()
     * @see \RectorPrefix202512\print_node()
     *
     * @param \PhpParser\Node\Expr\New_ $node
     *
     * @noinspection PhpParamsInspection
     */
    public function refactor(Node $node): ?Node
    {
        if (
            /** 暂不处理匿名类 `new class() extends Exception {}` 的情况. */
            !$node->class instanceof Name
            || !is_subclass_of($class = $this->getName($node->class), \Throwable::class)
            || is_subclass_of_all($class, $this->implements)
        ) {
            return null;
        }

        return new New_(
            new Class_(
                null,
                [
                    // 'extends' => new FullyQualified($class),
                    'extends' => $node->class,
                    'implements' => collect($this->implements)
                        ->filter(static fn (string $implement): bool => !is_subclass_of($class, $implement))
                        ->sort()
                        ->map(static fn (string $implement): FullyQualified => new FullyQualified($implement))
                        ->all(),
                ],
                $node->class->getAttributes()
            ),
            $node->getArgs()
        );
    }

    public function provideMinPhpVersion(): int
    {
        return PhpVersion::PHP_70;
    }

    /**
     * @param list<class-string> $configuration
     */
    public function configure(array $configuration): void
    {
        // Assert::allIsAOf($configuration, \Throwable::class);
        Assert::allStringNotEmpty($configuration);

        $this->implements = $configuration;
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
                    new Exception('Testing');
                    PHP,
                <<<'PHP'
                    /** @noinspection ALL */
                    new class('Testing') extends Exception implements \Guanguans\RectorRules\Contract\ThrowableContract
                    {
                    };
                    PHP,
                [ThrowableContract::class],
            ),
        ];
    }
}
