<?php

/** @noinspection ALL */
namespace Guanguans\RectorRules\Rector\Param;

use Guanguans\RectorRules\Rector\AbstractRector;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\VersionBonding\Contract\MinPhpVersionInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;

final class AddSensitiveParameterAttributeRector extends AbstractRector implements ConfigurableRectorInterface, MinPhpVersionInterface
{
    public const SENSITIVE_PARAMETERS = 'sensitive_parameters';

    protected function codeSamples(): array
    {
        return [
            new ConfiguredCodeSample(
                <<<'PHP'
                    /** @noinspection ALL */
                    namespace Guanguans\RectorRulesTests\Rector\Param\AddSensitiveParameterAttributeRector\Fixture;

                    function login($username, $password): void
                    {
                    }

                    class ApplyAttributeToMethods
                    {
                        public function login($username, $password): void
                        {
                        }
                    }
                    PHP,
                <<<'PHP'
                    /** @noinspection ALL */
                    namespace Guanguans\RectorRulesTests\Rector\Param\AddSensitiveParameterAttributeRector\Fixture;

                    function login($username, #[\SensitiveParameter]
                    $password): void
                    {
                    }

                    class ApplyAttributeToMethods
                    {
                        public function login($username, #[\SensitiveParameter]
                        $password): void
                        {
                        }
                    }
                    PHP,
                [/*self::SENSITIVE_PARAMETERS => ['password']*/]
            ),
        ];
    }
}

?>
-----
<?php

/** @noinspection ALL */
namespace Guanguans\RectorRules\Rector\Param;

use Guanguans\RectorRules\Rector\AbstractRector;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\VersionBonding\Contract\MinPhpVersionInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;

final class AddSensitiveParameterAttributeRector extends AbstractRector implements ConfigurableRectorInterface, MinPhpVersionInterface
{
    public const SENSITIVE_PARAMETERS = 'sensitive_parameters';

    protected function codeSamples(): array
    {
        return [
            new ConfiguredCodeSample(
                <<<'PHP'
                    /** @noinspection ALL */
                    namespace Guanguans\RectorRulesTests\Rector\Param\AddSensitiveParameterAttributeRector\Fixture;

                    function login($username, $password): void
                    {
                    }

                    class ApplyAttributeToMethods
                    {
                        public function login($username, $password): void
                        {
                        }
                    }
                    PHP,
                <<<'PHP'
                    /** @noinspection ALL */
                    namespace Guanguans\RectorRulesTests\Rector\Param\AddSensitiveParameterAttributeRector\Fixture;

                    function login($username, #[\SensitiveParameter]
                    $password): void
                    {
                    }

                    class ApplyAttributeToMethods
                    {
                        public function login($username, #[\SensitiveParameter]
                        $password): void
                        {
                        }
                    }
                    PHP,
                [self::SENSITIVE_PARAMETERS => ['password']]
            ),
        ];
    }
}

?>
