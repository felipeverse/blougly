<?php

namespace Blougly\Domain\Contents;

use Blougly\Domain\ValueObjects\Metadata;

final class ParsedContent
{
    public function __construct(
        public readonly Metadata $metadata,
        public readonly string $rawBody,
        public readonly string $htmlBody,
    ) {}
}
