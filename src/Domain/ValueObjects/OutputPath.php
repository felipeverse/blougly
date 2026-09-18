<?php

namespace Blougly\Domain\ValueObjects;

final class OutputPath
{
    private function __construct(
        public readonly string $relativePath
    ) {}

    public static function fromRelativePath(string $path): self
    {
        $path = str_replace('\\', '/', $path);

        if ($path === '') {
            throw new \InvalidArgumentException('Output path cannot be empty.');
        }

        if (str_starts_with($path, '/') || self::isAbsolutePath($path)) {
            throw new \InvalidArgumentException('Output path must be relative.');
        }

        $segments = explode('/', $path);

        foreach ($segments as $segment) {
            if ($segment === '' || $segment === '.' || $segment === '..') {
                throw new \InvalidArgumentException("Invalid segment in output path: {$path}");
            }
        }

        return new self($path);
    }

    private static function isAbsolutePath(string $path): bool
    {
        // Check for Unix/Linux/macOS absolute paths
        if (str_starts_with($path, '/') || str_starts_with($path, '\\')) {
            return true;
        }

        // Check for Windows absolute paths (e.g., "C:\..." or "D:/...")
        if (strlen($path) >= 3 && ctype_alpha($path[0]) && $path[1] === ':' && ($path[2] === '\\' || $path[2] === '/')) {
            return true;
        }

        return false;
    }
}
