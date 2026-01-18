<?php

/** @noinspection ALL */
namespace Guanguans\PHPStanRules\Rule\File;

use Guanguans\PHPStanRules\Rule\AbstractRule;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;

final class ForbiddenSideEffectsRule extends AbstractRule
{
    public function getNodeType(): string
    {
        return FileNode::class;
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
namespace Guanguans\PHPStanRules\Rule\File;

use Guanguans\PHPStanRules\Rule\AbstractRule;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;

final class ForbiddenSideEffectsRule extends AbstractRule
{
    public function getNodeType(): string
    {
        return FileNode::class;
    }

    /**
     * @param \PHPStan\Node\FileNode $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        return [];
    }
}

?>
