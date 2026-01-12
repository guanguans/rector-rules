<?php

/** @noinspection ALL */
namespace Guanguans\RectorRules\Rector\New_;

use Guanguans\RectorRules\Rector\AbstractRector;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;

final class NewExceptionToNewAnonymousExtendsExceptionImplementsRector extends AbstractRector implements ConfigurableRectorInterface
{
    protected function codeSamples(): array
    {
        return [
            new ConfiguredCodeSample(
                <<<'PHP'
                /** @noinspection ALL */
                // new Exception('Testing');
                PHP,
                <<<'PHP'
                /** @noinspection ALL */
                // new class('Testing') extends Exception implements \Guanguans\RectorRules\Contract\ThrowableContract
                // {
                // };
                PHP,
                [/*ThrowableContract::class*/],
            ),
        ];
    }
}

?>
-----
<?php

/** @noinspection ALL */
namespace Guanguans\RectorRules\Rector\New_;

use Guanguans\RectorRules\Rector\AbstractRector;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;

final class NewExceptionToNewAnonymousExtendsExceptionImplementsRector extends AbstractRector implements ConfigurableRectorInterface
{
    protected function codeSamples(): array
    {
        return [
            new ConfiguredCodeSample(
                <<<'PHP'
                /** @noinspection ALL */
                new Exception('Testing');
                PHP,
                <<<'PHP'
                /** @noinspection ALL */
                new class('Testing') extends Exception implements \Guanguans\RectorRules\Contract\ThrowableContract
                {
                };
                PHP,
                [ThrowableContract::class],
            ),
        ];
    }
}

?>
