<?php

/** @noinspection ALL */
namespace Guanguans\RectorRules\Rector\Array_;

use Guanguans\RectorRules\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;

final class SimplifyListIndexRector extends AbstractRector
{
    protected function codeSamples(): array
    {
        return [
            new CodeSample(
                <<<'PHP'
                /** @noinspection ALL */
                [0 => 'foo', 1 => 'bar', 2 => 'baz'];
                // [0 => 'foo', 'bar', 2 => 'baz'];
                PHP,
                <<<'PHP'
                /** @noinspection ALL */
                ['foo', 'bar', 'baz'];
                // ['foo', 'bar', 'baz'];
                PHP,
            ),
        ];
    }
}

?>
-----
<?php

/** @noinspection ALL */
namespace Guanguans\RectorRules\Rector\Array_;

use Guanguans\RectorRules\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;

final class SimplifyListIndexRector extends AbstractRector
{
    protected function codeSamples(): array
    {
        return [
            new CodeSample(
                <<<'PHP'
                /** @noinspection ALL */
                [0 => 'foo', 1 => 'bar', 2 => 'baz'];
                [0 => 'foo', 'bar', 2 => 'baz'];
                PHP,
                <<<'PHP'
                /** @noinspection ALL */
                ['foo', 'bar', 'baz'];
                ['foo', 'bar', 'baz'];
                PHP,
            ),
        ];
    }
}

?>
