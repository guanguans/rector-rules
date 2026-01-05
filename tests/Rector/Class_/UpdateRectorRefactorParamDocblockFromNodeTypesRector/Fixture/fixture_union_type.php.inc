<?php

/** @noinspection ALL */
namespace Guanguans\RectorRules\Rector\Name;

use Guanguans\RectorRules\Rector\AbstractRector;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;

final class RenameToPsrNameRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [
            FuncCall::class,
            Identifier::class,
            Name::class,
            Variable::class,
        ];
    }

    public function refactor(Node $node): ?Node
    {
        return null;
    }
}

?>
-----
<?php

/** @noinspection ALL */
namespace Guanguans\RectorRules\Rector\Name;

use Guanguans\RectorRules\Rector\AbstractRector;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;

final class RenameToPsrNameRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [
            FuncCall::class,
            Identifier::class,
            Name::class,
            Variable::class,
        ];
    }

    /**
     * @param \PhpParser\Node\Expr\FuncCall|\PhpParser\Node\Expr\Variable|\PhpParser\Node\Identifier|\PhpParser\Node\Name $node
     */
    public function refactor(Node $node): ?Node
    {
        return null;
    }
}

?>
