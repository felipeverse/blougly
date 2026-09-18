<?php

use Blougly\Adapters\Inbound\CLI\BuildCommand;
use Blougly\Application\BuildPaths;
use Blougly\Bootstrap\Container;
use Blougly\Ports\Inbound\SiteBuilder;

require __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/../src/helpers.php';

$projectRoot = dirname(__DIR__);
$sourceDir = "{$projectRoot}/source";
$outputDir = "{$projectRoot}/public";

$buildPaths = new BuildPaths(
    $sourceDir,
    $outputDir,
);

$container = Container::build($buildPaths);

$builder = $container->get(SiteBuilder::class);

$command = new BuildCommand($builder);

exit($command->execute());
