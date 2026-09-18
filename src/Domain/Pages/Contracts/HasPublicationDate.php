<?php

namespace Blougly\Domain\Pages\Contracts;

interface HasPublicationDate
{
    public function publishedAt(): ?\DateTimeImmutable;
}
