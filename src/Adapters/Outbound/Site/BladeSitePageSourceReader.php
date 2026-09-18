<?php

namespace Blougly\Adapters\Outbound\Site;

use Blougly\Domain\Pages\SitePage;
use Blougly\Domain\ValueObjects\OutputPath;
use Blougly\Domain\ValueObjects\SourceKind;
use Blougly\Domain\ValueObjects\SourceLocation;
use Blougly\Ports\Outbound\FileSystem;
use Blougly\Ports\Outbound\SitePageSourceReader;

final class BladeSitePageSourceReader implements SitePageSourceReader
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

            if (!str_ends_with($filePath, '.blade.php')) {
                throw new \RuntimeException("Unsupported file in site/: {$filePath}. Supported: .blade.php");
            }

            $relativePath = substr($filePath, strlen($prefix));

            $sourceLocation = SourceLocation::fromRelativePath(SourceKind::Site, $relativePath);
            $outputPath = OutputPath::fromRelativePath(preg_replace('/\.blade.php$/', '.html', $relativePath));

            yield new SitePage($sourceLocation, $outputPath);
        }
    }
}
