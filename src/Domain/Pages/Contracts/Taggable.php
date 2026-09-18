<?php

namespace Blougly\Domain\Pages\Contracts;

interface Taggable
{
    public function hasTag(string $tag): bool;
}
