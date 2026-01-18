<?php

/** @noinspection ALL */
namespace Guanguans\PHPStanRules\Rule\Class_;

use Guanguans\PHPStanRules\Rule\AbstractMixedNodeTypeRule;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;


final class ExceptionMustImplementNativeThrowableRule extends AbstractMixedNodeTypeRule
{
    protected function getNodeTypes(): array
    {
        return [
            Class_::class,
            New_::class,
        ];
    }

    protected function rawProcessNode(Node $node, Scope $scope): array
    {
        return [];
    }
}

?>
-----
<?php

/** @noinspection ALL */
namespace Guanguans\PHPStanRules\Rule\Class_;

use Guanguans\PHPStanRules\Rule\AbstractMixedNodeTypeRule;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;


final class ExceptionMustImplementNativeThrowableRule extends AbstractMixedNodeTypeRule
{
    protected function getNodeTypes(): array
    {
        return [
            Class_::class,
            New_::class,
        ];
    }

    /**
     * @param \PhpParser\Node\Expr\New_|\PhpParser\Node\Stmt\Class_ $node
     */
    protected function rawProcessNode(Node $node, Scope $scope): array
    {
        return [];
    }
}

?>
