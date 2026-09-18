<?php

namespace Blougly\Ports\Outbound;

use Blougly\Domain\Data\DataSet;

interface DataSourceReader
{
    /** @return iterable<DataSet> */
    public function readAll(string $dir): iterable;
}
