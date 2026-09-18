<?php

namespace Blougly\Domain\Pages\Contracts;

interface Draftable
{
    public function isDraft(): bool;
}
