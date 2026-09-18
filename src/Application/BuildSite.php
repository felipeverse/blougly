<?php

namespace Blougly\Application;

use Blougly\Application\BuildPaths;
use Blougly\Application\LoadSources;
use Blougly\Application\PageRenderer;
use Blougly\Ports\Inbound\BuildResult;
use Blougly\Ports\Inbound\SiteBuilder;
use Blougly\Ports\Outbound\AssetPublisher;
use Blougly\Ports\Outbound\DataPublisher;
use Blougly\Ports\Outbound\FileSystem;

final class BuildSite implements SiteBuilder
{
    public function __construct(
        private readonly FileSystem $fs,
        private readonly LoadSources $loadSources,
        private readonly BuildPaths $buildPaths,
        private readonly AssetPublisher $assetPublisher,
        private readonly DataPublisher $dataPublisher,
        private readonly PageRenderer $pageRenderer,
    ) {}

    public function build(): BuildResult
    {
        $start = microtime(true);

        $sources = $this->loadSources->execute();

        $this->fs->delete($this->buildPaths->outputDir);

        foreach ($sources->assets as $asset) {
            $this->assetPublisher->publish($asset);
        }

        foreach ($sources->data as $data) {
            $this->dataPublisher->publish($data);
        }

        $contents = $sources->contents->withoutDrafts();

        $context = [
            'site' => $sources->siteConfig->all(),
            'data' => $sources->data->all(),
            'contents' => $contents
        ];

        foreach ($contents as $page) {
            $html = $this->pageRenderer->render($page, $context);
            $this->fs->write(
                $this->buildPaths->outputDir . DIRECTORY_SEPARATOR . $page->outputPath()->relativePath,
                $html
            );
        }

        foreach ($sources->sitePages as $sitePage) {
            $html = $this->pageRenderer->render($sitePage, $context);
            $this->fs->write(
                $this->buildPaths->outputDir . DIRECTORY_SEPARATOR . $sitePage->outputPath()->relativePath,
                $html
            );
        }

        return new BuildResult(
            $this->buildPaths->outputDir,
            microtime(true) - $start
        );
    }
}
