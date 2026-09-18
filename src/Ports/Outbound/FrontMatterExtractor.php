<?php

namespace Blougly\Ports\Outbound;

use Blougly\Domain\ValueObjects\FrontMatterResult;

interface FrontMatterExtractor
{
    public function extract(string $raw): FrontMatterResult;
}
