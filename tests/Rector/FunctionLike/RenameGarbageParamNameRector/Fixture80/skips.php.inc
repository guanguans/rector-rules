<?php

/** @noinspection ALL */
class Baz
{
    // Skip promoted property parameter.
    public function __construct(private Bar $bar)
    {
    }
}
