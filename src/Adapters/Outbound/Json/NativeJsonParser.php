<?php

namespace Blougly\Adapters\Outbound\Json;

use Blougly\Ports\Outbound\JsonParser;

final class NativeJsonParser implements JsonParser
{
    public function parse(string $content): array
    {
        return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }
}
