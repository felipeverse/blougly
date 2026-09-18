<?php

namespace Blougly\Adapters\Outbound\FileSystem;

use Blougly\Application\BuildPaths;
use Blougly\Domain\Assets\Asset;
use Blougly\Ports\Outbound\AssetPublisher;
use Blougly\Ports\Outbound\FileSystem;

final class LocalAssetPublisher implements AssetPublisher
{
    public function __construct(
        private readonly FileSystem $fs,
        private readonly BuildPaths $buildPaths,
    ) {}

    public function publish(Asset $asset): void
    {
        $sourcePath = $this->buildPaths->assetsDir() . DIRECTORY_SEPARATOR . $asset->sourceLocation()->relativePath;
        $outputPath = $this->buildPaths->outputDir . DIRECTORY_SEPARATOR . $asset->outputPath()->relativePath;

        $this->fs->copy($sourcePath, $outputPath);
    }
}
