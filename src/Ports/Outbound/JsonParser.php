<?php

namespace Blougly\Ports\Outbound;

interface JsonParser
{
    public function parse(string $content): array;
}
