<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\FuncCall\FuncCallToStaticCallRector\Fixture;

class Fixture
{
    public function someMethod()
    {
        \view('template', []);
        \SomeNamespaced\view('template', []);
    }
}

?>
-----
<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\FuncCall\FuncCallToStaticCallRector\Fixture;

class Fixture
{
    public function someMethod()
    {
        \SomeStaticClass::render('template', []);
        \AnotherStaticClass::render('template', []);
    }
}

?>
