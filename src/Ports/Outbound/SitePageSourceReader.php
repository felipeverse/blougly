<?php

namespace Blougly\Ports\Outbound;

use Blougly\Domain\Pages\SitePage;

interface SitePageSourceReader
{
    /** @return iterable<SitePage> */
    public function readAll(string $dir): iterable;
}
