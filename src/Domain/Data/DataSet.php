<?php

namespace Blougly\Domain\Data;

use Blougly\Domain\ValueObjects\OutputPath;
use Blougly\Domain\ValueObjects\SourceLocation;

final class DataSet
{
    public function __construct(
        private readonly SourceLocation $sourceLocation,
        private readonly OutputPath $outputPath,
        private readonly array $values,
    ) {}

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->values[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->values;
    }

    public function sourceLocation(): SourceLocation
    {
        return $this->sourceLocation;
    }

    public function outputPath(): OutputPath
    {
        return $this->outputPath;
    }
}
