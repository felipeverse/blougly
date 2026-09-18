<?php

namespace Blougly\Adapters\Outbound\Json;

use Blougly\Domain\ValueObjects\SiteConfig;
use Blougly\Ports\Outbound\FileSystem;
use Blougly\Ports\Outbound\JsonParser;
use Blougly\Ports\Outbound\SiteConfigSourceReader;

final class JsonSiteConfigSourceReader implements SiteConfigSourceReader
{
    public function __construct(
        private readonly FileSystem $fileSystem,
        private readonly JsonParser $jsonParser,
    ) {}

    public function read(string $sourceDir): SiteConfig
    {
        $configPath = $sourceDir . DIRECTORY_SEPARATOR . 'config.json';

        if (!$this->fileSystem->exists($configPath)) {
            return SiteConfig::empty();
        }

        try {
            return SiteConfig::fromArray(
                $this->jsonParser->parse(
                    $this->fileSystem->read($configPath)
                )
            );
        } catch (\InvalidArgumentException | \JsonException $e) {
            throw new \RuntimeException("Invalid config file in {$configPath}", 0, $e);
        }
    }
}
