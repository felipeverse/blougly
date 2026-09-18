<?php

namespace Blougly\Application;

use Blougly\Domain\ValueObjects\SourceKind;

final class BuildPaths
{
    public function __construct(
        public readonly string $sourceDir,
        public readonly string $outputDir
    ) {}

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
