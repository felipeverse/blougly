<?php

namespace Blougly\Adapters\Outbound\FileSystem;

use Blougly\Domain\Assets\Asset;
use Blougly\Domain\ValueObjects\OutputPath;
use Blougly\Domain\ValueObjects\SourceKind;
use Blougly\Domain\ValueObjects\SourceLocation;
use Blougly\Ports\Outbound\AssetSourceReader;
use Blougly\Ports\Outbound\FileSystem;

final class LocalAssetSourceReader implements AssetSourceReader
{
    public function __construct(private readonly FileSystem $fileSystem) {}

    public function readAll(string $dir): iterable
    {
        if (!$this->fileSystem->exists($dir)) {
            return;
        }

        $prefix = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        foreach ($this->fileSystem->allFiles($dir) as $filePath) {
            if (str_starts_with(basename($filePath), '.')) {
                continue;
            }

            $relativePath = substr($filePath, strlen($prefix));

            $sourceLocation = SourceLocation::fromRelativePath(SourceKind::Assets, $relativePath);
            $outputPath = OutputPath::fromRelativePath(SourceKind::Assets->value . DIRECTORY_SEPARATOR . $relativePath);

            yield new Asset($sourceLocation, $outputPath);
        }
    }
}
