<?php

/** @noinspection ALL */
static fn(string $class, string $file): bool => true;

abstract class Foo
{
    abstract protected function classMethodNode(Class_ $classNode): ?ClassMethod;
}
