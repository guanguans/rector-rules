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

namespace Guanguans\RectorRules\Support;

use Rector\RuleDoc\Exception\ShouldNotHappenException;

/**
 * @see \Rector\RuleDoc\RuleDefinition
 * @see \Symplify\RuleDocGenerator\ValueObject\RuleDefinition
 */
trait RuleDefinition
{
    private ?string $ruleClass = null;

    public function setRuleClass(string $ruleClass): void
    {
        $this->ruleClass = $ruleClass;
    }

    /**
     * @throws \Rector\RuleDoc\Exception\ShouldNotHappenException
     *
     * @noinspection ThrowRawExceptionInspection
     */
    public function getRuleClass(): string
    {
        if (null === $this->ruleClass) {
            throw new ShouldNotHappenException;
        }

        return $this->ruleClass;
    }

    /**
     * @throws \Rector\RuleDoc\Exception\ShouldNotHappenException
     *
     * @noinspection ThrowRawExceptionInspection
     */
    public function getRuleShortClass(): string
    {
        if (null === $this->ruleClass) {
            throw new ShouldNotHappenException;
        }

        // get short class name
        return basename(str_replace('\\', '/', $this->ruleClass));
    }
}
