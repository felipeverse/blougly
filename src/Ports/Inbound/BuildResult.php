<?php

namespace Blougly\Ports\Inbound;

final class BuildResult
{
    public function __construct(
        public readonly string $outputDir,
        public readonly float $elapsedSeconds = 0.0,
    ) {}
}
