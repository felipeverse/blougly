<?php

use Blougly\Adapters\Inbound\CLI\BuildCommand;
use Blougly\Application\BuildPaths;
use Blougly\Bootstrap\Container;
use Blougly\Ports\Inbound\SiteBuilder;

require __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/../src/helpers.php';

/**
 * @param array<int, string> $argv
 * @return array{source: string, output: string}
 */
function parseConsoleArgs(array $argv): array
{
    $options = [
        'source' => './source',
        'output' => './public',
    ];

    for ($i = 1; $i < count($argv); $i++) {
        $arg = $argv[$i];

        if (!str_starts_with($arg, '--')) {
            consoleError("Unexpected argument: {$arg}");
        }

        $name = substr($arg, 2);
        $value = null;

        if (str_contains($name, '=')) {
            [$name, $value] = explode('=', $name, 2);

            if ($value === '') {
                consoleError("Option --{$name} requires a value");
            }
        }

        if (!array_key_exists($name, $options)) {
            consoleError("Unknown option --{$name}");
        }

        if ($value === null) {
            $next = $argv[$i + 1] ?? null;

            if ($next === null || $next === '' || str_starts_with($next, '--')) {
                consoleError("Option --{$name} requires a value");
            }

            $value = $next;
            $i++;
        }

        $options[$name] = $value;
    }

    return $options;
}

function consoleError(string $message): never
{
    fwrite(STDERR, "Error: {$message}\n\n");
    fwrite(STDERR, "Usage: php bin/console.php [--source=<dir>] [--output=<dir>]\n\n");
    fwrite(STDERR, "Options:\n");
    fwrite(STDERR, "  --source  Path to the source directory (default: ./source)\n");
    fwrite(STDERR, "  --output  Path to the output directory (default: ./public)\n");

    exit(1);
}

$options = parseConsoleArgs($argv);

if (!is_dir($options['source'])) {
    consoleError("Source directory does not exist: {$options['source']}");
}

$buildPaths = BuildPaths::create(
    $options['source'],
    $options['output']
);

$container = Container::build($buildPaths);

$builder = $container->get(SiteBuilder::class);

$command = new BuildCommand($builder);

exit($command->execute());
