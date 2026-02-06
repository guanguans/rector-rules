<?php

/** @noinspection ALL */

use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Symfony\Component\OptionsResolver\OptionsResolver as OptionsResolver;

abstract class Foo
{
    // Skip abstract method.
    abstract public function run($param): void;

    // Skip nop method.
    protected function configureOptionsResolver(OptionsResolver $optionsResolver): void
    {
        // Configure options resolver...
    }
}

interface Bar
{
    // Skip interface method.
    public function run($param): void;
}

final class FooRector extends AbstractRector
{
    // Skip has prototype method.
    public function refactor(Node $node): ?Node
    {
        return null;
    }
}
