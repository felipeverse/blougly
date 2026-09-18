<?php

namespace Blougly\Application;

use Blougly\Domain\Pages\ContentPage;
use Blougly\Domain\Pages\Contracts\Page;
use Blougly\Domain\Pages\SitePage;
use Blougly\Ports\Outbound\TemplateRenderer;

final class PageRenderer
{
    public function __construct(
        private readonly TemplateRenderer $templateRenderer
    ) {}

    public function render(Page $page, array $context): string
    {
        $data = array_merge($context, $page->templateData());

        return match (true) {
            $page instanceof SitePage => $this->templateRenderer->renderSitePage($page, $data),
            $page instanceof ContentPage && $page->templateName() !== null => $this->templateRenderer->renderTemplate(
                $page->templateName(),
                $data,
            ),
            $page instanceof ContentPage => $page->htmlBody(),
            default => throw new \RuntimeException('Unsupported page type: ' . $page::class),
        };
    }
}
