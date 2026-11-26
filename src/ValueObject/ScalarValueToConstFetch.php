<?php

declare(strict_types=1);

/**
 * Copyright (c) 2025-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/rector-rules
 */

namespace Guanguans\RectorRules\ValueObject;

/**
 * @api used in deprecated ScalarValueToConstFetchRector configs
 */
final class ScalarValueToConstFetch
{
    /**
     * @readonly
     *
     * @var \PhpParser\Node\Scalar\Float_|\PhpParser\Node\Scalar\Int_|\PhpParser\Node\Scalar\String_
     */
    private $scalar;

    /**
     * @readonly
     *
     * @var \PhpParser\Node\Expr\ClassConstFetch|\PhpParser\Node\Expr\ConstFetch
     */
    private $constFetch;

    /**
     * @param \PhpParser\Node\Scalar\Float_|\PhpParser\Node\Scalar\Int_|\PhpParser\Node\Scalar\String_ $scalar
     * @param \PhpParser\Node\Expr\ClassConstFetch|\PhpParser\Node\Expr\ConstFetch $constFetch
     */
    public function __construct($scalar, $constFetch)
    {
        $this->scalar = $scalar;
        $this->constFetch = $constFetch;
    }

    /**
     * @return \PhpParser\Node\Scalar\Float_|\PhpParser\Node\Scalar\Int_|\PhpParser\Node\Scalar\String_
     */
    public function getScalar()
    {
        return $this->scalar;
    }

    /**
     * @return \PhpParser\Node\Expr\ClassConstFetch|\PhpParser\Node\Expr\ConstFetch
     */
    public function getConstFetch()
    {
        return $this->constFetch;
    }
}
