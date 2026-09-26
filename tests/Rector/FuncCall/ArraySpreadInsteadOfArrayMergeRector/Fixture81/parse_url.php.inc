<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector\Fixture81;

class ParseUrl
{
    public function run()
    {
        return array_merge($this->parseUrl($url), $this->parseUrl($redirectLocation));
    }

    /**
     * @return array<string, string>
     */
    private function parseUrl(string $url): array
    {
        $urlParts = parse_url($url);

        return array_filter($urlParts);
    }
}
?>
-----
<?php

/** @noinspection ALL */
namespace Guanguans\RectorRulesTests\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector\Fixture81;

class ParseUrl
{
    public function run()
    {
        return [...$this->parseUrl($url), ...$this->parseUrl($redirectLocation)];
    }

    /**
     * @return array<string, string>
     */
    private function parseUrl(string $url): array
    {
        $urlParts = parse_url($url);

        return array_filter($urlParts);
    }
}
?>
