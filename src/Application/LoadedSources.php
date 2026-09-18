<?php

namespace Blougly\Application;

use Blougly\Domain\Assets\Asset;
use Blougly\Domain\Data\DataCollection;
use Blougly\Domain\Pages\PageCollection;
use Blougly\Domain\Pages\SitePage;
use Blougly\Domain\ValueObjects\SiteConfig;

final class LoadedSources
{
    public function __construct(
        public readonly SiteConfig $siteConfig,
        public readonly PageCollection $contents,
        public readonly DataCollection $data,
        /** @var Asset[] $assets */
        public readonly array $assets,
        /** @var SitePage[] $sitePages */
        public readonly array $sitePages,
    ) {}
}
