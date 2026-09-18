<?php

namespace Blougly\Ports\Outbound;

use Blougly\Domain\Assets\Asset;

interface AssetSourceReader
{
    /** @return iterable<Asset> */
    public function readAll(string $dir): iterable;
}
