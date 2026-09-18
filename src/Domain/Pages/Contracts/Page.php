<?php

namespace Blougly\Domain\Pages\Contracts;

use Blougly\Domain\ValueObjects\OutputPath;
use Blougly\Domain\ValueObjects\SourceLocation;

interface Page
{
    public function sourceLocation(): SourceLocation;
    public function outputPath(): OutputPath;
    public function templateData(): array;
}
