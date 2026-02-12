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

namespace Guanguans\RectorRules\Rector;

use PhpParser\Node;
use Rector\Config\RectorConfig;
use Rector\DependencyInjection\LazyContainerFactory;

/**
 * @see \PhpCsFixer\AbstractProxyFixer
 */
abstract class AbstractProxyRector extends AbstractRector
{
    // /** @param list<mixed> $arguments */
    // public function __call(string $name, array $arguments)
    // {return $this->makeProxyRector()->{$name}(...$arguments);}

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * {@inheritDoc}
     */
    final public function getNodeTypes(): array
    {
        return $this->makeProxyRector()->getNodeTypes();
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    final public function refactor(Node $node)
    {
        return $this->rawRefactor($node);
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return null|list<Node>|NodeVisitor::REMOVE_NODE|\PhpParser\Node
     */
    protected function rawRefactor(Node $node)
    {
        return $this->makeProxyRector()->refactor($node);
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return \Rector\Contract\Rector\ConfigurableRectorInterface&\Rector\Rector\AbstractRector
     */
    protected function makeProxyRector(): \Rector\Rector\AbstractRector
    {
        return $this->makeRectorConfig()->make($this->proxyRectorClass());
    }

    /**
     * @see \Rector\Testing\PHPUnit\AbstractLazyTestCase::getContainer()
     */
    protected function makeRectorConfig(): RectorConfig
    {
        static $rectorConfig;

        if ($rectorConfig instanceof RectorConfig) {
            return $rectorConfig;
        }

        $rectorConfig = (new LazyContainerFactory)->create();
        $rectorConfig->singletonIf($this->proxyRectorClass());
        $rectorConfig->boot();

        return $rectorConfig;
    }

    /**
     * @return class-string<\Rector\Rector\AbstractRector>
     */
    abstract protected function proxyRectorClass(): string;
}
