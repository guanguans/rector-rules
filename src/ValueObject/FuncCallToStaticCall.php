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

use Rector\Validation\RectorAssert;

final class FuncCallToStaticCall
{
    /** @readonly */
    private string $oldFuncName;

    /** @readonly */
    private string $newClassName;

    /** @readonly */
    private string $newMethodName;

    public function __construct(
        string $oldFuncName,
        string $newClassName,
        string $newMethodName
    ) {
        $this->oldFuncName = $oldFuncName;
        $this->newClassName = $newClassName;
        $this->newMethodName = $newMethodName;
        RectorAssert::functionName($oldFuncName);

        RectorAssert::className($newClassName);
        RectorAssert::methodName($newMethodName);
    }

    public function getOldFuncName(): string
    {
        return $this->oldFuncName;
    }

    public function getNewClassName(): string
    {
        return $this->newClassName;
    }

    public function getNewMethodName(): string
    {
        return $this->newMethodName;
    }
}
