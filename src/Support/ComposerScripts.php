<?php

/** @noinspection EfferentObjectCouplingInspection */
/** @noinspection PhpUnused */
/** @noinspection PhpUnusedAliasInspection */
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

use Composer\Script\Event;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\Nop;
use PHPStan\PhpDocParser\Ast\AbstractNodeVisitor;
use PHPStan\PhpDocParser\Ast\Attribute;
use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\NodeTraverser;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\BetterPhpDocParser\ValueObject\PhpDocAttributeKey;
use Rector\Config\RectorConfig;
use Rector\DependencyInjection\LazyContainerFactory;
use Rector\PhpParser\Parser\SimplePhpParser;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @see https://github.com/laravel/framework/blob/12.x/src/Illuminate/Foundation/ComposerScripts.php
 *
 * @internal
 *
 * @property \Symfony\Component\Console\Output\ConsoleOutput $output
 *
 * @method void configureIO(InputInterface $input, OutputInterface $output)
 */
final class ComposerScripts
{
    /**
     * @see \PhpCsFixer\Hasher
     * @see \PhpCsFixer\Utils
     */
    private function __construct() {}

    /**
     * @see https://github.com/rectorphp/rector-src/blob/main/scoper.php
     * @see \PhpParser\Builder\
     * @see \PhpParser\BuilderHelpers
     * @see \Rector\Application\ChangedNodeScopeRefresher
     * @see \Rector\BetterPhpDocParser\Comment\CommentsMerger
     * @see \Rector\BetterPhpDocParser\PhpDocManipulator\
     * @see \Rector\Naming\ParamRenamer\
     * @see \Rector\Naming\PhpDoc\
     * @see \Rector\Naming\PropertyRenamer\
     * @see \Rector\Naming\VariableRenamer
     * @see \Rector\NodeAnalyzer\
     * @see \Rector\NodeAnalyzer\ExprAnalyzer
     * @see \Rector\NodeAnalyzer\ScopeAnalyzer
     * @see \Rector\NodeNameResolver\
     * @see \Rector\NodeTypeResolver\PhpDoc\NodeAnalyzer\DocBlockClassRenamer
     * @see \Rector\PhpDocParser\NodeTraverser\SimpleCallableNodeTraverser
     * @see \Rector\PhpParser\Comparing\
     * @see \Rector\PhpParser\Enum\NodeGroup
     * @see \Rector\PhpParser\NodeFinder\
     * @see \Rector\PhpParser\NodeTraverser\
     * @see \Rector\PhpParser\NodeVisitor\
     * @see \Rector\PhpParser\Parser\
     * @see \Rector\PhpParser\Printer\
     * @see \Rector\PostRector\Rector\
     * @see \Rector\Renaming\NodeManipulator\ClassRenamer
     *
     * @throws \ErrorException
     * @throws \ReflectionException
     *
     * @return int<0>|never-returns<1>
     *
     * @noinspection PhpDocSignatureInspection
     */
    public static function listFiles(Event $event): int
    {
        self::requireAutoload($event);

        require_once $event->getComposer()->getConfig()->get('vendor-dir').'/rector/rector/vendor/autoload.php';

        classes(
            static fn (string $class, string $file): bool => Str::of($class)->startsWith('Rector\\')
                && Str::of($class)->afterLast('\\')->contains([
                    'Better',
                    // 'Factory',
                    // 'Resolver',
                    // 'er',
                    // 'Renamer',
                ])
                && !Str::of($file)->contains([
                    '/rector-doctrine/',
                    '/rector-downgrade-php/',
                    '/rector-phpunit/',
                    '/rector-symfony/',
                    '/jack/',
                    '/swiss-knife/',
                    '/type-perfect/',
                ])
        )
            ->sortKeys()
            // ->groupBy(fn (string $class) => str($class)->explode('\\')->get(1))
            // ->keys()
            ->tap(static function (Collection $classes) use ($event): void {
                $event->getIO()->info('');
                $event->getIO()->info("Found {$classes->count()} files:");
                $event->getIO()->info('');
            })
            ->each(static function (\ReflectionClass $reflectionClass) use ($event): void {
                $event->getIO()->info(Str::remove(getcwd().\DIRECTORY_SEPARATOR, $reflectionClass->getFileName()));
            });

        $event->getIO()->info('');
        $event->getIO()->info('No errors');

        return 0;
    }

    /**
     * @see vendor/nikic/php-parser/bin/php-parse
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @noinspection ForgottenDebugOutputInspection
     * @noinspection DebugFunctionUsageInspection
     * @noinspection PhpVoidFunctionResultUsedInspection
     */
    public static function phpdocParse(Event $event): void
    {
        self::requireAutoload($event);

        $rectorConfig = self::makeRectorConfig();
        $path = self::makeArgvInput()->getParameterOption('--path', false, true);

        if ($path) {
            $node = $rectorConfig->make(SimplePhpParser::class)->parseFile($path)[0];
        } else {
            do {
                $docComment = $event->getIO()->ask(\sprintf('Please provide a doc comment to parse:%s', \PHP_EOL));
            } while (blank($docComment));

            $node = tap(new Nop)->setDocComment(new Doc($docComment));
        }

        $phpDocNode = $rectorConfig->make(PhpDocInfoFactory::class)->createFromNodeOrEmpty($node)->getPhpDocNode();
        (new NodeTraverser([
            new class extends AbstractNodeVisitor {
                /**
                 * @noinspection PhpMissingParentCallCommonInspection
                 */
                public function enterNode(Node $node): Node
                {
                    // $node->setAttribute(Attribute::ORIGINAL_NODE, null);
                    $node->setAttribute(PhpDocAttributeKey::ORIG_NODE, null);
                    $node->setAttribute(PhpDocAttributeKey::PARENT, null);

                    return $node;
                }
            },
        ]))->traverse($phpDocNode->children);

        dump($phpDocNode, (string) $phpDocNode);
    }

    public static function makeRectorConfig(): RectorConfig
    {
        static $rectorConfig;

        return $rectorConfig ??= (new LazyContainerFactory)->create();
    }

    /**
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public static function requireAutoload(Event $event, ?bool $enableDebugging = null): void
    {
        $enableDebugging ??= (new ArgvInput)->hasParameterOption('-vvv', true);
        $enableDebugging and $event->getIO()->enableDebugging(microtime(true));
        (fn () => $this->output->setVerbosity(OutputInterface::VERBOSITY_DEBUG))->call($event->getIO());

        require_once $event->getComposer()->getConfig()->get('vendor-dir').\DIRECTORY_SEPARATOR.'autoload.php';
    }

    public static function makeArgvInput(?array $argv = null, ?InputDefinition $inputDefinition = null): ArgvInput
    {
        static $argvInput;

        return $argvInput ??= new ArgvInput($argv, $inputDefinition);
    }

    /**
     * @see \Rector\Console\Style\SymfonyStyleFactory
     */
    public static function makeSymfonyStyle(?InputInterface $input = null, ?OutputInterface $output = null): SymfonyStyle
    {
        static $symfonyStyle;

        if (
            $symfonyStyle instanceof SymfonyStyle
            && (
                !$input instanceof InputInterface
                || (string) \Closure::bind(
                    static fn (SymfonyStyle $symfonyStyle): InputInterface => $symfonyStyle->input,
                    null,
                    SymfonyStyle::class
                )($symfonyStyle) === (string) $input
            )
            && (
                !$output instanceof OutputInterface
                || \Closure::bind(
                    static fn (SymfonyStyle $symfonyStyle): OutputInterface => $symfonyStyle->output,
                    null,
                    SymfonyStyle::class
                )($symfonyStyle) === $output
            )
        ) {
            return $symfonyStyle;
        }

        $input ??= new ArgvInput;
        $output ??= new ConsoleOutput;

        // to configure all -v, -vv, -vvv options without memory-lock to Application run() arguments
        (fn () => $this->configureIO($input, $output))->call(new Application);

        // --debug or --xdebug is called
        if ($input->hasParameterOption(['--debug', '--xdebug'], true)) {
            $output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        }

        // disable output for testing
        if (self::isRunningInTesting()) {
            $output->setVerbosity(OutputInterface::VERBOSITY_QUIET);
        }

        return $symfonyStyle = new SymfonyStyle($input, $output);
    }

    public static function isRunningInTesting(): bool
    {
        return 'testing' === getenv('ENV');
    }
}
