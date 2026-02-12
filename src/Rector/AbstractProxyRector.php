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
    protected RectorConfig $rectorConfig;
    protected \Rector\Rector\AbstractRector $proxyRector;

    /**
     * @see \Rector\Testing\PHPUnit\AbstractLazyTestCase::getContainer()
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct()
    {
        $rectorConfig = (new LazyContainerFactory)->create();
        $rectorConfig->boot();
        $this->rectorConfig = $rectorConfig;
        $this->proxyRector = clone $rectorConfig->make($this->proxyRectorClass());
    }

    /**
     * @param list<mixed> $arguments
     *
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return $this->proxyRector->{$name}(...$arguments);
    }

    /**
     * @throws \ReflectionException
     *
     * {@inheritDoc}
     */
    final public function getNodeTypes(): array
    {
        /** @var \ReflectionClass<\Rector\Rector\AbstractRector> $reflectionClass */
        $reflectionClass = new \ReflectionClass($this->proxyRectorClass());

        return $reflectionClass->newInstanceWithoutConstructor()->getNodeTypes();
    }

    /**
     * {@inheritDoc}
     */
    final public function refactor(Node $node)
    {
        return $this->applyRefactor($node);
    }

    /**
     * @return null|list<Node>|NodeVisitor::REMOVE_NODE|\PhpParser\Node
     */
    protected function applyRefactor(Node $node)
    {
        return $this->proxyRector->refactor($node);
    }

    /**
     * @return class-string<\Rector\Rector\AbstractRector>
     */
    abstract protected function proxyRectorClass(): string;
}
