<?php

namespace Blougly\Ports\Outbound;

use Blougly\Domain\ValueObjects\SiteConfig;

interface SiteConfigSourceReader
{
    public function read(string $sourceDir): SiteConfig;
}
