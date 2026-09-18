<?php

namespace Blougly\Ports\Outbound;

use Blougly\Domain\Assets\Asset;

interface AssetPublisher
{
    public function publish(Asset $asset): void;
}
