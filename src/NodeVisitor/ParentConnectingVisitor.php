<?php

/** @noinspection PhpMissingParentCallCommonInspection */
declare(strict_types=1);

/**
 * Copyright (c) 2025-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/rector-rules
 */

namespace Guanguans\RectorRules\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use Rector\BetterPhpDocParser\ValueObject\PhpDocAttributeKey;
use Rector\Contract\PhpParser\DecoratingNodeVisitorInterface;

/**
 * Visitor that connects a child node to its parent node.
 *
 * With <code>$weakReferences=false</code> on the child node, the parent node can be accessed through
 * <code>$node->getAttribute('parent')</code>.
 *
 * With <code>$weakReferences=true</code> the attribute name is "weak_parent" instead.
 *
 * @see \PhpParser\NodeVisitor\ParentConnectingVisitor
 */
final class ParentConnectingVisitor extends NodeVisitorAbstract implements DecoratingNodeVisitorInterface
{
    /** @var list<Node> */
    private array $stack = [];
    private bool $weakReferences;

    /**
     * @api
     */
    public function __construct(bool $weakReferences = false)
    {
        $this->weakReferences = $weakReferences;
    }

    public function beforeTraverse(array $nodes): void
    {
        $this->stack = [];
    }

    /**
     * @noinspection OffsetOperationsInspection
     */
    public function enterNode(Node $node): void
    {
        if ([] !== $this->stack) {
            $parent = $this->stack[\count($this->stack) - 1];

            if ($this->weakReferences) {
                $node->setAttribute('weak_parent', \WeakReference::create($parent));
            } else {
                $node->setAttribute(PhpDocAttributeKey::PARENT, $parent);
            }
        }

        $this->stack[] = $node;
    }

    public function leaveNode(Node $node): void
    {
        array_pop($this->stack);
    }
}
