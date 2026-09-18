<?php

namespace Blougly\Adapters\Outbound\Markdown;

use Blougly\Ports\Outbound\MarkdownConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter as CommonMarkConverter;

final class CommonMarkMarkdownConverter implements MarkdownConverter
{
    private CommonMarkConverter $converter;

    public function __construct(?array $config = [])
    {
        $environment = new Environment(array_merge([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ], $config));

        $environment->addExtension(new CommonMarkCoreExtension());

        $this->converter = new CommonMarkConverter($environment);
    }

    public function convert(string $markdown): string
    {
        return $this->converter->convert($markdown)->getContent();
    }
}
