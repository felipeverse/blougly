<?php

namespace Blougly\Domain\Pages;

use \DateTimeImmutable;
use Blougly\Domain\Contents\ParsedContent;
use Blougly\Domain\Pages\Contracts\Draftable;
use Blougly\Domain\Pages\Contracts\HasPublicationDate;
use Blougly\Domain\Pages\Contracts\Page;
use Blougly\Domain\Pages\Contracts\Taggable;
use Blougly\Domain\ValueObjects\Metadata;
use Blougly\Domain\ValueObjects\OutputPath;
use Blougly\Domain\ValueObjects\Slug;
use Blougly\Domain\ValueObjects\SourceLocation;

final class ContentPage implements Page, HasPublicationDate, Draftable, Taggable
{
    private function __construct(
        private readonly SourceLocation $sourceLocation,
        private readonly OutputPath $outputPath,
        private readonly Metadata $metadata,
        private readonly string $rawBody,
        private readonly string $htmlBody,
        private readonly ?string $templateName,
        private readonly bool $draft,
        private readonly ?DateTimeImmutable $createdAt,
        private readonly ?DateTimeImmutable $updatedAt,
        private readonly ?DateTimeImmutable $publishedAt,
    ) {}

    public static function fromParsed(
        SourceLocation $sourceLocation,
        ParsedContent $parsedContent,
    ): self {
        $metadata = $parsedContent->metadata;
        $publishedAt = self::parseMetadataDate($metadata->get('published'), 'published', $sourceLocation);

        return new self(
            $sourceLocation,
            self::resolveOutputPath($sourceLocation, $metadata, $publishedAt),
            $metadata,
            $parsedContent->rawBody,
            $parsedContent->htmlBody,
            self::resolveTemplate($sourceLocation, $metadata),
            self::resolveDraft($metadata, $sourceLocation),
            self::parseMetadataDate($metadata->get('created'), 'created', $sourceLocation),
            self::parseMetadataDate($metadata->get('updated'), 'updated', $sourceLocation),
            $publishedAt,
        );
    }

    private static function parseMetadataDate(mixed $value, string $field, SourceLocation $sourceLocation): ?\DateTimeImmutable
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return \DateTimeImmutable::createFromInterface($value);
        }

        if (!is_string($value) && !is_int($value)) {
            throw new \InvalidArgumentException("Invalid '{$field}' in {$sourceLocation->relativePath}");
        }

        $value = (string) $value;

        foreach (['U', 'Y-m-d', 'Y-m-d H:i:s', \DateTimeImmutable::ATOM,] as $format) {
            $date = \DateTimeImmutable::createFromFormat($format, $value);
            $errors = \DateTimeImmutable::getLastErrors();

            $hasErrors = is_array($errors) && ($errors['warning_count'] > 0 || $errors['error_count'] > 0);

            if ($date !== false && !$hasErrors) {
                return $date;
            }
        }

        throw new \InvalidArgumentException(
            "Invalid '{$field}' in {$sourceLocation->relativePath}"
        );
    }

    private static function resolveOutputPath(
        SourceLocation $sourceLocation,
        Metadata $metadata,
        ?\DateTimeImmutable $publishedAt
    ): OutputPath {
        $pathParts = [];

        $directory = self::slugifyPath(dirname($sourceLocation->relativePath));

        if ($directory !== '') {
            $pathParts[] = $directory;
        }

        if ($publishedAt !== null) {
            $pathParts[] = $publishedAt->format('Y');
        }

        $suffix = pathinfo($sourceLocation->relativePath, PATHINFO_EXTENSION);
        $fallbackTitle = $metadata->get(
            'title',
            basename($sourceLocation->relativePath, ".{$suffix}")
        );
        $slug = $metadata->get('slug')
            ? Slug::fromExplicit($metadata->get('slug'))
            : Slug::fromText($fallbackTitle);
        $pathParts[] = "{$slug}.html";

        return OutputPath::fromRelativePath(implode('/', $pathParts));
    }

    private static function slugifyPath(string $path): string
    {
        if ($path === '.') {
            return '';
        }

        $segments = array_map(Slug::fromText(...), explode('/', $path));

        return implode('/', $segments);
    }

    private static function resolveDraft(Metadata $metadata, SourceLocation $sourceLocation,): bool
    {
        if (!$metadata->has('draft')) {
            return false;
        }

        $draft = $metadata->get('draft');

        if (!is_bool($draft)) {
            throw new \InvalidArgumentException(
                "Invalid 'draft' in {$sourceLocation->relativePath}. Expected boolean."
            );
        }

        return $draft;
    }

    private static function resolveTemplate(SourceLocation $sourceLocation, Metadata $metadata): ?string
    {
        $template = $metadata->get('template');

        if ($template === null) {
            return null;
        }

        if (!is_string($template) || $template === '') {
            throw new \RuntimeException(
                "Missing or invalid 'template' in {$sourceLocation->relativePath}"
            );
        }

        return $template;
    }

    public function sourceLocation(): SourceLocation
    {
        return $this->sourceLocation;
    }

    public function outputPath(): OutputPath
    {
        return $this->outputPath;
    }

    public function hasTag(string $tag): bool
    {
        return in_array($tag, $this->metadata->get('tags', []), true);
    }

    public function templateData(): array
    {
        return array_merge(
            $this->metadata->all(),
            ['body' => $this->htmlBody]
        );
    }

    public function templateName(): ?string
    {
        return $this->templateName;
    }

    public function createdAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function publishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function isDraft(): bool
    {
        return $this->draft;
    }

    public function metadata(): Metadata
    {
        return $this->metadata;
    }

    public function rawBody(): string
    {
        return $this->rawBody;
    }

    public function htmlBody(): string
    {
        return $this->htmlBody;
    }
}
