<?php

namespace Blougly\Ports\Outbound;

use Blougly\Domain\Pages\ContentPage;

interface ContentSourceReader
{
    /** @return iterable<ContentPage> */
    public function readAll(string $dir): iterable;
}
