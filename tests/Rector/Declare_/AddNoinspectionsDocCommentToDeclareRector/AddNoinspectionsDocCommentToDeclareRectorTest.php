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

namespace Guanguans\RectorRulesTests\Rector\Declare_\AddNoinspectionsDocCommentToDeclareRector;

use Guanguans\RectorRulesTests\Rector\AbstractRectorTestCase;

final class AddNoinspectionsDocCommentToDeclareRectorTest extends AbstractRectorTestCase
{
    /**
     * @dataProvider provideCases()
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provideCases')]
    public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    public static function provideCases(): iterable
    {
        return self::yieldFilesFromDirectory(__DIR__.'/Fixture');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/configured_rule.php';
    }
}
