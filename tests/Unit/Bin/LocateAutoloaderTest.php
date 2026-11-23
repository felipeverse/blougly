<?php

require_once __DIR__ . '/../../../bin/locate-autoloader.php';

it('resolves the repo autoloader when neither composer marker is present', function () {
    $binDir = $this->tempDir . '/vendor/acme/tool/bin';
    mkdir($binDir, 0777, true);
    mkdir($this->tempDir . '/vendor/acme/tool/vendor', 0777, true);
    touch($this->tempDir . '/vendor/acme/tool/vendor/autoload.php');
    touch($this->tempDir . '/vendor/autoload.php');

    expect(locateAutoloader($binDir))->toBe($binDir . '/../vendor/autoload.php');
});

it('resolves the consumer autoloader when only the consumer composer marker is present', function () {
    $binDir = $this->tempDir . '/vendor/acme/tool/bin';
    mkdir($binDir, 0777, true);
    mkdir($this->tempDir . '/vendor/composer', 0777, true);
    touch($this->tempDir . '/vendor/composer/installed.json');
    touch($this->tempDir . '/vendor/autoload.php');
    mkdir($this->tempDir . '/vendor/acme/tool/vendor', 0777, true);
    touch($this->tempDir . '/vendor/acme/tool/vendor/autoload.php');

    expect(locateAutoloader($binDir))->toBe($binDir . '/../../../autoload.php');
});

it('resolves the repo autoloader when the repo composer marker wins', function () {
    $binDir = $this->tempDir . '/vendor/acme/tool/bin';
    mkdir($binDir, 0777, true);
    mkdir($this->tempDir . '/vendor/composer', 0777, true);
    touch($this->tempDir . '/vendor/composer/installed.json');
    mkdir($this->tempDir . '/vendor/acme/tool/vendor/composer', 0777, true);
    touch($this->tempDir . '/vendor/acme/tool/vendor/composer/installed.json');
    touch($this->tempDir . '/vendor/autoload.php');
    touch($this->tempDir . '/vendor/acme/tool/vendor/autoload.php');

    expect(locateAutoloader($binDir))->toBe($binDir . '/../vendor/autoload.php');
});

it('returns null when no autoloader exists', function () {
    $binDir = $this->tempDir . '/vendor/acme/tool/bin';
    mkdir($binDir, 0777, true);

    expect(locateAutoloader($binDir))->toBeNull();
});

it('skips a directory named autoload.php', function () {
    $binDir = $this->tempDir . '/vendor/acme/tool/bin';
    mkdir($binDir, 0777, true);
    mkdir($this->tempDir . '/vendor/acme/tool/vendor/autoload.php', 0777, true);

    expect(locateAutoloader($binDir))->toBeNull();
});
