<?php

namespace Blougly\Ports\Outbound;

use Blougly\Domain\Data\DataSet;

interface DataPublisher
{
    public function publish(DataSet $dataSet): void;
}
