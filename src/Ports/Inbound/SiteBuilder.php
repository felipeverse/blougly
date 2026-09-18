<?php

namespace Blougly\Ports\Inbound;

interface SiteBuilder
{
    public function build(): BuildResult;
}
