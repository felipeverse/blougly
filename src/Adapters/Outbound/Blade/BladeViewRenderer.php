<?php

namespace Blougly\Adapters\Outbound\Blade;

use Blougly\Domain\Pages\SitePage;
use Blougly\Ports\Outbound\TemplateRenderer;
use Jenssegers\Blade\Blade;

final class BladeViewRenderer implements TemplateRenderer
{
    private const SITE_NAMESPACE = 'site';

    private readonly Blade $blade;

    public function __construct(string $viewsPath, string $sitePath)
    {
        $cachePath = sys_get_temp_dir() . '/blougly/templates/cache';
        $this->blade = new Blade((array) $viewsPath, $cachePath);
        $this->blade->addNamespace(self::SITE_NAMESPACE, $sitePath);
    }

    public function renderTemplate(string $name, array $context): string
    {
        return $this->blade->render($name, $context);
    }

    public function renderSitePage(SitePage $sitePage, array $context): string
    {
        $name = preg_replace(
            '/\.blade.php$/',
            '',
            $sitePage->sourceLocation()->relativePath
        );

        $name = str_replace('/', '.', $name);

        return $this->blade->render(
            self::SITE_NAMESPACE . "::{$name}",
            $context
        );
    }
}
