<?php

namespace Blougly\Application;

use Blougly\Domain\Data\DataCollection;
use Blougly\Domain\Pages\PageCollection;
use Blougly\Ports\Outbound\AssetSourceReader;
use Blougly\Ports\Outbound\ContentSourceReader;
use Blougly\Ports\Outbound\DataSourceReader;
use Blougly\Ports\Outbound\SiteConfigSourceReader;
use Blougly\Ports\Outbound\SitePageSourceReader;

final class LoadSources
{
    public function __construct(
        private readonly BuildPaths $buildPaths,
        private readonly SiteConfigSourceReader $siteConfigSourceReader,
        private readonly ContentSourceReader $contentSourceReader,
        private readonly DataSourceReader $dataSourceReader,
        private readonly AssetSourceReader $assetSourceReader,
        private readonly SitePageSourceReader $sitePageSourceReader,
    ) {}

    public function execute(): LoadedSources
    {
        return new LoadedSources(
            siteConfig: $this->siteConfigSourceReader->read($this->buildPaths->sourceDir),
            contents: new PageCollection([...$this->contentSourceReader->readAll($this->buildPaths->contentsDir())]),
            data: DataCollection::fromDataSets($this->dataSourceReader->readAll($this->buildPaths->dataDir())),
            assets: [...$this->assetSourceReader->readAll($this->buildPaths->assetsDir())],
            sitePages: [...$this->sitePageSourceReader->readAll($this->buildPaths->siteDir())],
        );
    }
}
