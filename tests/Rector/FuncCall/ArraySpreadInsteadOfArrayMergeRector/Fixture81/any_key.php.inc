<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector\Fixture81;

class AnyKey
{
    /**
     * @return array<int>
     */
    public function getA(): array
    {
		return [1, 2, 3];
    }

    /**
     * @return array<int>
     */
    public function getB(): array
    {
    	return [4, 5, 6];
    }

    public function run()
    {
        $a = $this->getA();
        $b = $this->getB();

        return array_merge($a, $b);
    }
}

?>
-----
<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector\Fixture81;

class AnyKey
{
    /**
     * @return array<int>
     */
    public function getA(): array
    {
		return [1, 2, 3];
    }

    /**
     * @return array<int>
     */
    public function getB(): array
    {
    	return [4, 5, 6];
    }

    public function run()
    {
        $a = $this->getA();
        $b = $this->getB();

        return [...$a, ...$b];
    }
}

?>
