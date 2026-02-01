<?php

/** @noinspection ALL */
abstract class Foo
{
    // Skip abstract method.
    abstract public function run($param): void;
}

interface Bar
{
    // Skip interface method.
    public function run($param): void;
}

class Baz
{
    // Skip promoted property parameter.
    public function __construct(private Bar $bar)
    {
    }
}

use PhpParser\Node;
use Rector\Rector\AbstractRector;

final class FooRector extends AbstractRector
{
    // Skip has prototype method.
    public function refactor(Node $node): ?Node
    {
        return null;
    }
}
