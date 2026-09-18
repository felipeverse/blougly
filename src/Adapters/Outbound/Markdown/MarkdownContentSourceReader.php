<?php

namespace Blougly\Adapters\Outbound\Markdown;

use Blougly\Domain\Contents\ParsedContent;
use Blougly\Domain\Pages\ContentPage;
use Blougly\Domain\ValueObjects\SourceKind;
use Blougly\Domain\ValueObjects\SourceLocation;
use Blougly\Ports\Outbound\ContentSourceReader;
use Blougly\Ports\Outbound\FileSystem;
use Blougly\Ports\Outbound\FrontMatterExtractor;
use Blougly\Ports\Outbound\MarkdownConverter;

final class MarkdownContentSourceReader implements ContentSourceReader
{
    public function __construct(
        private readonly FileSystem $fileSystem,
        private readonly FrontMatterExtractor $frontMatterExtractor,
        private readonly MarkdownConverter $markdownConverter
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

            if (!str_ends_with(basename($filePath), '.md')) {
                throw new \RuntimeException("Unsupported file in {$dir}/: {$filePath}. Supported: .md");
            }

            $relativePath = substr($filePath, strlen($prefix));
            $sourceLocation = SourceLocation::fromRelativePath(SourceKind::Contents, $relativePath);

            $extracted = $this->frontMatterExtractor->extract($this->fileSystem->read($filePath));
            $parsedContent = new ParsedContent(
                $extracted->metadata,
                $extracted->body,
                $this->markdownConverter->convert($extracted->body),
            );

            yield ContentPage::fromParsed($sourceLocation, $parsedContent);
        }
    }
}
