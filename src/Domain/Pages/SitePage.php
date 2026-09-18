<?php

namespace Blougly\Domain\Pages;

use Blougly\Domain\Pages\Contracts\Page;
use Blougly\Domain\ValueObjects\OutputPath;
use Blougly\Domain\ValueObjects\SourceLocation;

final class SitePage implements Page
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

    public function templateData(): array
    {
        return [];
    }
}
