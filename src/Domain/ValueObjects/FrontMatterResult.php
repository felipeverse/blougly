<?php

namespace Blougly\Domain\ValueObjects;

final class FrontMatterResult
{
    public function __construct(
        public readonly Metadata $metadata,
        public readonly string $body,
    ) {}
}
