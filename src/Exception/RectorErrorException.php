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

namespace Guanguans\RectorRules\Exception;

use Guanguans\RectorRules\Contract\ThrowableContract;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use PhpParser\Error;
use Rector\Rector\AbstractRector;

/**
 * @see \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException
 */
final class RectorErrorException extends Error implements ThrowableContract
{
    /**
     * @throws \ReflectionException
     */
    public function __construct(AbstractRector $rector, string $message, array $attributes = [])
    {
        parent::__construct(
            \sprintf(
                '[%s:%s%s] %s',
                (new \ReflectionObject($rector))->getShortName(),
                (string) Str::of((fn (): string => $this->file->getFilePath())->bindTo($rector, $rector)())
                    // ->chopStart(getcwd().\DIRECTORY_SEPARATOR)
                    // ->replaceStart(getcwd().\DIRECTORY_SEPARATOR, '')
                    ->whenStartsWith(
                        $cmd = getcwd().\DIRECTORY_SEPARATOR,
                        static fn (Stringable $stringable): Stringable => $stringable->replaceFirst($cmd, '')
                    ),
                isset($attributes['startLine']) ? ":{$attributes['startLine']}" : '',
                $message
            ),
            $attributes
        );
    }
}
