<?php

declare(strict_types=1);

namespace Kickflip;

use Illuminate\Console\OutputStyle;
use Kickflip\Enums\ConsoleVerbosity;
use Illuminate\Config\Repository;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\Support\Facades\Log;

class Logger
{
    private static OutputStyle $consoleOutput;

    public static function timing(string $methodName, ?string $static = null): void
    {
        /**
         * @var Repository $timingsRepo
         */
        $timingsRepo = app('kickflipTimings');
        $index = Str::of($methodName)->afterLast('\\')->replace('::', '.');
        if (null !== $static) {
            $index = $index->replaceFirst(
                '.',
                Str::of($static)->afterLast('\\')->prepend('.extended.')->append('.')
            );
        }
        $timingsRepo->set((string) $index, microtime(true));
    }


    public static function setOutput(OutputStyle $output)
    {
        static::$consoleOutput = $output;
    }

    public static function debug(string $message): void
    {
        if (ConsoleVerbosity::debug() <= app('kickflipCli')->get('output.verbosity')) {
            Log::debug($message);
            if (isset(static::$consoleOutput)) {
                static::$consoleOutput->warning($message);
            }
        }
    }

    /**
     * @param string[] $headers
     * @param array<string[]> $rows
     */
    public static function veryVerboseTable(array $headers, array $rows): void
    {
        if (ConsoleVerbosity::veryVerbose() <= app('kickflipCli')->get('output.verbosity') && isset(static::$consoleOutput)) {
            static::$consoleOutput->table($headers, $rows);
        }
    }

    public static function veryVerbose(string $message): void
    {
        if (ConsoleVerbosity::veryVerbose() <= app('kickflipCli')->get('output.verbosity')) {
            Log::debug($message);
            if (isset(static::$consoleOutput)) {
                static::$consoleOutput->info($message);
            }
        }
    }

    public static function verbose(string $message): void
    {
        if (ConsoleVerbosity::verbose() <= app('kickflipCli')->get('output.verbosity')) {
            Log::info($message);
            if (isset(static::$consoleOutput)) {
                static::$consoleOutput->info($message);
            }
        }
    }

    public static function info(string $message): void
    {
        if (ConsoleVerbosity::normal() <= app('kickflipCli')->get('output.verbosity')) {
            Log::info($message);
            if (isset(static::$consoleOutput)) {
                static::$consoleOutput->writeln($message);
            }
        }
    }
}
