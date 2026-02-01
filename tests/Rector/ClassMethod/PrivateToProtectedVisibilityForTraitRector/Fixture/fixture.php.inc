<?php

/** @noinspection ALL */
trait Foo
{
    private function run(): void
    {
    }
}
?>
-----
<?php

/** @noinspection ALL */
trait Foo
{
    protected function run(): void
    {
    }
}
?>
