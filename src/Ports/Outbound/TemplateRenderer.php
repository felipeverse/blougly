<?php

namespace Blougly\Ports\Outbound;

use Blougly\Domain\Pages\SitePage;

interface TemplateRenderer
{
    /** @param array<string, mixed> $context */
    public function renderTemplate(string $template, array $context): string;

    /** @param array<string, mixed> $context */
    public function renderSitePage(SitePage $sitePage, array $context): string;
}
