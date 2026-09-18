<?php

namespace Blougly\Adapters\Inbound\CLI;

use Blougly\Ports\Inbound\SiteBuilder;

final class BuildCommand
{
    public function __construct(private readonly SiteBuilder $siteBuilder) {}

    public function execute(): int
    {
        try {
            $result = $this->siteBuilder->build();

            fwrite(STDOUT, "Built in {$result->elapsedSeconds}s to {$result->outputDir}\n");
            return 0;
        } catch (\Throwable $e) {
            fwrite(STDERR, "Error: {$e->getMessage()}\n");
            fwrite(STDERR, "File: {$e->getFile()}:{$e->getLine()}\n");
            return 1;
        }
    }
}
