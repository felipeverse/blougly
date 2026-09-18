<?php

namespace Blougly\Adapters\Outbound\Markdown;

use Blougly\Domain\ValueObjects\FrontMatterResult;
use Blougly\Domain\ValueObjects\Metadata;
use Blougly\Ports\Outbound\FrontMatterExtractor;
use League\CommonMark\Extension\FrontMatter\Data\SymfonyYamlFrontMatterParser;
use League\CommonMark\Extension\FrontMatter\FrontMatterParser;

final class CommonMarkFrontMatterExtractor implements FrontMatterExtractor
{
    private FrontMatterParser $parser;

    public function __construct()
    {
        $this->parser = new FrontMatterParser(new SymfonyYamlFrontMatterParser());
    }

    public function extract(string $raw): FrontMatterResult
    {
        $result = $this->parser->parse($raw);

        $metadata = Metadata::fromArray($result->getFrontMatter() ?? []);

        $body = $result->getContent();

        return new FrontMatterResult($metadata, $body);
    }
}
