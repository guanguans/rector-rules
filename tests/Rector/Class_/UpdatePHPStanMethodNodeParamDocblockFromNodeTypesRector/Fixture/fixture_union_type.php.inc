<?php

/** @noinspection ALL */
namespace Guanguans\PHPStanRules\Rule;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FunctionLike;
use PHPStan\Rules\Rule;

final class ForbiddenSideEffectsFunctionLikeRule extends Rule
{
    public function getNodeType(): string
    {
        return FunctionLike::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        return [];
    }
}

?>
-----
<?php

/** @noinspection ALL */
namespace Guanguans\PHPStanRules\Rule;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FunctionLike;
use PHPStan\Rules\Rule;

final class ForbiddenSideEffectsFunctionLikeRule extends Rule
{
    public function getNodeType(): string
    {
        return FunctionLike::class;
    }

    /**
     * @param \PhpParser\Node\FunctionLike $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        return [];
    }
}

?>
