<?php

declare(strict_types=1);

/**
 * Copyright (c) 2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/rector-rules
 */

namespace Guanguans\RectorRules\Rector\Cases\Rule;

use Ergebnis\PhpCsFixer\Config\Name;
use PhpParser\Node;
use Rector\NodeNameResolver\NodeNameResolver;

final class FuncCallRule implements RuleInterface
{
    private NodeNameResolver $nodeNameResolver;

    public function __construct(NodeNameResolver $nodeNameResolver)
    {
        $this->nodeNameResolver = $nodeNameResolver;
    }

    /**
     * @param \PhpParser\Node\Expr\FuncCall $node
     */
    public function shouldCase(Node $node): bool
    {
        return $node->name instanceof Name;
    }

    /**
     * @param \PhpParser\Node\Expr\FuncCall $node
     */
    public function resolveName(Node $node): string
    {
        return $this->nodeNameResolver->getShortName($node);
    }

    /**
     * @param \PhpParser\Node\Expr\FuncCall $node
     */
    public function applyName(Node $node, string $name): Node
    {
        $node->name->name = $name;

        return $node;
    }
}
