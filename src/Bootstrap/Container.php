<?php

namespace Blougly\Bootstrap;

use Blougly\Adapters\Outbound\Blade\BladeViewRenderer;
use Blougly\Adapters\Outbound\FileSystem\LocalAssetPublisher;
use Blougly\Adapters\Outbound\FileSystem\LocalAssetSourceReader;
use Blougly\Adapters\Outbound\FileSystem\LocalDataPublisher;
use Blougly\Adapters\Outbound\FileSystem\LocalFileSystem;
use Blougly\Adapters\Outbound\Json\JsonDataSourceReader;
use Blougly\Adapters\Outbound\Json\JsonSiteConfigSourceReader;
use Blougly\Adapters\Outbound\Json\NativeJsonParser;
use Blougly\Adapters\Outbound\Markdown\CommonMarkFrontMatterExtractor;
use Blougly\Adapters\Outbound\Markdown\CommonMarkMarkdownConverter;
use Blougly\Adapters\Outbound\Markdown\MarkdownContentSourceReader;
use Blougly\Adapters\Outbound\Site\BladeSitePageSourceReader;
use Blougly\Application\BuildPaths;
use Blougly\Application\BuildSite;
use Blougly\Application\LoadSources;
use Blougly\Application\PageRenderer;
use Blougly\Ports\Inbound\SiteBuilder;
use Blougly\Ports\Outbound\AssetPublisher;
use Blougly\Ports\Outbound\AssetSourceReader;
use Blougly\Ports\Outbound\ContentSourceReader;
use Blougly\Ports\Outbound\DataPublisher;
use Blougly\Ports\Outbound\DataSourceReader;
use Blougly\Ports\Outbound\FileSystem;
use Blougly\Ports\Outbound\FrontMatterExtractor;
use Blougly\Ports\Outbound\JsonParser;
use Blougly\Ports\Outbound\MarkdownConverter;
use Blougly\Ports\Outbound\SiteConfigSourceReader;
use Blougly\Ports\Outbound\SitePageSourceReader;
use Blougly\Ports\Outbound\TemplateRenderer;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

final class Container
{
    public static function build(BuildPaths $buildPaths): ContainerInterface
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions([
            BuildPaths::class => \DI\factory(fn() => $buildPaths),
            TemplateRenderer::class => \DI\factory(function (BuildPaths $buildPaths): TemplateRenderer {
                return new BladeViewRenderer($buildPaths->viewsDir(), $buildPaths->siteDir());
            }),
            FileSystem::class => \DI\autowire(LocalFileSystem::class),
            FrontMatterExtractor::class => \DI\autowire(CommonMarkFrontMatterExtractor::class),
            MarkdownConverter::class => \DI\autowire(CommonMarkMarkdownConverter::class),
            JsonParser::class => \DI\autowire(NativeJsonParser::class),
            LoadSources::class => \DI\autowire(),
            BuildSite::class => \DI\autowire(),
            PageRenderer::class => \DI\autowire(),
            AssetPublisher::class => \DI\autowire(LocalAssetPublisher::class),
            DataPublisher::class => \DI\autowire(LocalDataPublisher::class),
            SiteBuilder::class => \DI\get(BuildSite::class),
            SiteConfigSourceReader::class => \DI\autowire(JsonSiteConfigSourceReader::class),
            ContentSourceReader::class => \DI\autowire(MarkdownContentSourceReader::class),
            DataSourceReader::class => \DI\autowire(JsonDataSourceReader::class),
            AssetSourceReader::class => \DI\autowire(LocalAssetSourceReader::class),
            SitePageSourceReader::class => \DI\autowire(BladeSitePageSourceReader::class),
        ]);

        return $builder->build();
    }
}
