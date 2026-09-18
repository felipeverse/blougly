<?php

namespace Blougly\Adapters\Outbound\Json;

use Blougly\Domain\Data\DataSet;
use Blougly\Domain\ValueObjects\OutputPath;
use Blougly\Domain\ValueObjects\SourceKind;
use Blougly\Domain\ValueObjects\SourceLocation;
use Blougly\Ports\Outbound\DataSourceReader;
use Blougly\Ports\Outbound\FileSystem;
use Blougly\Ports\Outbound\JsonParser;

final class JsonDataSourceReader implements DataSourceReader
{
    public function __construct(
        private readonly FileSystem $fileSystem,
        private readonly JsonParser $jsonParser,
    ) {}

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

            if (!str_ends_with(basename($filePath), '.json')) {
                throw new \RuntimeException("Unsupported file in {$dir}/: {$filePath}. Supported: .json");
            }

            $relativePath = substr($filePath, strlen($prefix));
            $sourceLocation = SourceLocation::fromRelativePath(SourceKind::Data, $relativePath);
            $outputPath = OutputPath::fromRelativePath(SourceKind::Data->value . DIRECTORY_SEPARATOR . $relativePath);

            yield new DataSet(
                $sourceLocation,
                $outputPath,
                $this->jsonParser->parse($this->fileSystem->read($filePath))
            );
        }
    }
}
