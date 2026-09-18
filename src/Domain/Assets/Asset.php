<?php

namespace Blougly\Domain\Assets;

use Blougly\Domain\ValueObjects\OutputPath;
use Blougly\Domain\ValueObjects\SourceLocation;

final class Asset
{
    public function __construct(
        private readonly SourceLocation $sourceLocation,
        private readonly OutputPath $outputPath,
    ) {}

    public function sourceLocation(): SourceLocation
    {
        return $this->sourceLocation;
    }

    public function outputPath(): OutputPath
    {
        return $this->outputPath;
    }
}
