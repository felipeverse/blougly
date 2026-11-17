<?php

require_once __DIR__ . '/../../../bin/locate-autoloader.php';

it('returns the first candidate when it exists', function () {
    $first = $this->tempDir . '/autoload.php';
    touch($first);

    expect(locateAutoloader([$first]))->toBe($first);
});

it('falls back to the second candidate when the first is missing', function () {
    $missing = $this->tempDir . '/missing/autoload.php';
    $second = $this->tempDir . '/autoload.php';
    touch($second);

    expect(locateAutoloader([$missing, $second]))->toBe($second);
});

it('returns null when no candidate exists', function () {
    expect(locateAutoloader([
        $this->tempDir . '/missing-one.php',
        $this->tempDir . '/missing-two.php',
    ]))->toBeNull();
});

it('prefers the first candidate when both exist', function () {
    $first = $this->tempDir . '/first.php';
    $second = $this->tempDir . '/second.php';
    touch($first);
    touch($second);

    expect(locateAutoloader([$first, $second]))->toBe($first);
});

it('skips a candidate that is a directory', function () {
    $dir = $this->tempDir . '/autoload.php';
    mkdir($dir);
    $second = $this->tempDir . '/real.php';
    touch($second);

    expect(locateAutoloader([$dir, $second]))->toBe($second);
});

it('offers the repo and installed layout autoloaders', function () {
    expect(autoloaderCandidates('/app/vendor/acme/tool/bin'))->toBe([
        '/app/vendor/acme/tool/bin/../vendor/autoload.php',
        '/app/vendor/acme/tool/bin/../../../autoload.php',
    ]);
});
