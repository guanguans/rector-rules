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

namespace Guanguans\RectorRulesTests\Rector;

use Illuminate\Support\Str;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

abstract class AbstractRectorTestCase extends \Rector\Testing\PHPUnit\AbstractRectorTestCase
{
    final public function testRuleDefinition(): void
    {
        $reflectionClass = new \ReflectionClass(static::class);
        $reflectionClass = new \ReflectionClass(
            (string) Str::of($reflectionClass->getNamespaceName())->replace(['RectorRulesTests'], 'RectorRules')
        );
        $documentedRule = $reflectionClass->newInstanceWithoutConstructor();
        self::assertInstanceOf(DocumentedRuleInterface::class, $documentedRule);

        $ruleDefinition = $documentedRule->getRuleDefinition();
        self::assertInstanceOf(RuleDefinition::class, $ruleDefinition);

        /** @see \Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample */
        // foreach ($ruleDefinition->getCodeSamples() as $codeSample) {
        //     self::assertNotEmpty($codeSample->getBadCode());
        //     self::assertNotEmpty($codeSample->getGoodCode());
        //     self::assertNotSame($codeSample->getBadCode(), $codeSample->getGoodCode());
        // }
    }
}
