<?php

namespace Blougly\Adapters\Outbound\FileSystem;

use Blougly\Application\BuildPaths;
use Blougly\Domain\Data\DataSet;
use Blougly\Ports\Outbound\DataPublisher;
use Blougly\Ports\Outbound\FileSystem;

final class LocalDataPublisher implements DataPublisher
{
    public function __construct(
        private readonly FileSystem $fs,
        private readonly BuildPaths $buildPaths,
    ) {}

    public function publish(DataSet $dataSet): void
    {
        $sourcePath = $this->buildPaths->dataDir() . DIRECTORY_SEPARATOR . $dataSet->sourceLocation()->relativePath;
        $outputPath = $this->buildPaths->outputDir . DIRECTORY_SEPARATOR . $dataSet->outputPath()->relativePath;

        $this->fs->copy($sourcePath, $outputPath);
    }
}
