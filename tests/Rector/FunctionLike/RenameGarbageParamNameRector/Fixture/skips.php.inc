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
