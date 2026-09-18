<?php

namespace Blougly\Adapters\Outbound\FileSystem;

use Blougly\Ports\Outbound\FileSystem;


final class LocalFileSystem implements FileSystem
{
    public function read(string $path): string
    {
        if (!is_file($path)) {
            throw new \RuntimeException("Cannot read file: {$path}");
        }

        $content = file_get_contents($path);

        if ($content === false) {
            throw new \RuntimeException("Cannot read file: {$path}");
        }

        return $content;
    }

    public function write(string $path, string $content): void
    {
        $this->ensureDirectory(dirname($path));
        $result = file_put_contents($path, $content);

        if ($result === false) {
            throw new \RuntimeException("Cannot write file: {$path}");
        }
    }

    public function delete(string $path): void
    {
        if (!file_exists($path)) {
            return;
        }

        if (!is_dir($path)) {
            unlink($path);
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS,),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            if ($item->isDir()) {
                rmdir($item->getRealPath());
            } else {
                unlink($item->getRealPath());
            }
        }

        rmdir($path);
    }

    public function allFiles(string $directory): array
    {
        if (!is_dir($directory)) {
            throw new \RuntimeException("Directory does not exist: {$directory}");
        }

        $items = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(
            $directory,
            \RecursiveDirectoryIterator::SKIP_DOTS
        ));

        $files = [];

        foreach ($items as $item) {
            if ($item->isFile()) {
                $files[] = $item->getRealPath();
            }
        }

        return array_values($files);
    }

    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    public function copy(string $from, string $to): void
    {
        if (!file_exists($from)) {
            throw new \RuntimeException("The {$from} file does not exist");
        }

        if (!is_file($from)) {
            throw new \RuntimeException("The {$from} path is not a file");
        }

        $this->ensureDirectory(dirname($to));
        $result = copy($from, $to);

        if (!$result) {
            throw new \RuntimeException("Cannot copy {$from} to {$to}");
        }
    }

    private function ensureDirectory(string $path): void
    {
        if (!is_dir($path) && @!mkdir($path, 0755, true) && !is_dir($path)) {
            throw new \RuntimeException("Cannot create directory: {$path}");
        }
    }
}
