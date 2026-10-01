<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected string $tempDir;

    protected int $obLevel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->obLevel = ob_get_level();
        $this->tempDir = sys_get_temp_dir() . '/blougly-test-' . uniqid();
        mkdir($this->tempDir, 0777, true);
    }

    protected function tearDown(): void
    {
        while (ob_get_level() > $this->obLevel) {
            ob_end_clean();
        }

        $this->removeDirectory($this->tempDir);

        parent::tearDown();
    }

    protected function fixturePath(string $path = ''): string
    {
        $base = __DIR__ . '/fixtures/source';

        return $path === '' ? $base : $base . '/' . $path;
    }

    protected function expectedPath(string $path = ''): string
    {
        $base = __DIR__ . '/fixtures/expected';

        return $path === '' ? $base : $base . '/' . $path;
    }

    /**
     * @return list<string> sorted paths relative to $dir
     */
    protected function listFiles(string $dir): array
    {
        $files = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
        );

        foreach ($iterator as $file) {
            $files[] = substr($file->getPathname(), strlen($dir) + 1);
        }

        sort($files);

        return $files;
    }

    protected function copyDirectory(string $source, string $destination): void
    {
        mkdir($destination, 0777, true);

        foreach ($this->listFiles($source) as $file) {
            $target = $destination . '/' . $file;
            mkdir(dirname($target), 0777, true);
            copy($source . '/' . $file, $target);
        }
    }

    protected function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        foreach (scandir($dir) ?: [] as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . '/' . $item;

            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }

        rmdir($dir);
    }
}
