<?php

use Blougly\Application\BuildPaths;
use Blougly\Bootstrap\Container;
use Blougly\Ports\Inbound\SiteBuilder;

it('builds the fixture site identically to the expected output', function () {
    $buildPaths = BuildPaths::create($this->fixturePath(), $this->tempDir);
    $container = Container::build($buildPaths);
    $builder = $container->get(SiteBuilder::class);

    $builder->build();

    $expectedDir = $this->expectedPath();

    if (!is_dir($expectedDir)) {
        $this->copyDirectory($this->tempDir, $expectedDir);

        $this->markTestSkipped('Expected tree generated at ' . $expectedDir . '. Run again to verify.');
    }

    $expectedFiles = $this->listFiles($expectedDir);
    $actualFiles = $this->listFiles($this->tempDir);

    expect($actualFiles)->toBe($expectedFiles);

    foreach ($expectedFiles as $file) {
        expect(file_get_contents($this->tempDir . '/' . $file))
            ->toBe(file_get_contents($expectedDir . '/' . $file));
    }
});
