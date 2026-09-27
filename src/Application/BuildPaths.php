<?php

namespace Blougly\Application;

use Blougly\Domain\ValueObjects\SourceKind;

final class BuildPaths
{
    private function __construct(
        public readonly string $sourceDir,
        public readonly string $outputDir
    ) {}

    public static function create(string $sourceDir, string $outputDir): self
    {
        return new self(
            self::normalize($sourceDir),
            self::normalize($outputDir),
        );
    }

    private static function normalize(string $path): string
    {
        $resolved = realpath($path);

        if ($resolved !== false) {
            return $resolved;
        }

        $path = str_replace('\\', '/', $path);

        if (!str_starts_with($path, '/')) {
            $path = getcwd() . '/' . $path;
        }

        $segments = [];

        foreach (explode('/', $path) as $segment) {
            if ($segment === '' || $segment === '.') {
                continue;
            }

            if ($segment === '..') {
                array_pop($segments);
                continue;
            }

            $segments[] = $segment;
        }

        return '/' . implode('/', $segments);
    }

    public function assetsDir(): string
    {
        return $this->sourceDir . DIRECTORY_SEPARATOR . SourceKind::Assets->value;
    }

    public function contentsDir(): string
    {
        return $this->sourceDir . DIRECTORY_SEPARATOR . SourceKind::Contents->value;
    }

    public function dataDir(): string
    {
        return $this->sourceDir . DIRECTORY_SEPARATOR . SourceKind::Data->value;
    }

    public function siteDir(): string
    {
        return $this->sourceDir . DIRECTORY_SEPARATOR . SourceKind::Site->value;
    }

    public function viewsDir(): string
    {
        return $this->sourceDir . DIRECTORY_SEPARATOR . SourceKind::Views->value;
    }
}
