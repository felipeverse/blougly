<?php

namespace Blougly\Ports\Outbound;

interface MarkdownConverter
{
    public function convert(string $markdown): string;
}
