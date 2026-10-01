<?php

use Blougly\Domain\Contents\ParsedContent;
use Blougly\Domain\Pages\ContentPage;
use Blougly\Domain\Pages\PageCollection;
use Blougly\Domain\Pages\SitePage;
use Blougly\Domain\ValueObjects\Metadata;
use Blougly\Domain\ValueObjects\OutputPath;
use Blougly\Domain\ValueObjects\SourceKind;
use Blougly\Domain\ValueObjects\SourceLocation;

function sitePage(string $path): SitePage
{
    return new SitePage(
        SourceLocation::fromRelativePath(SourceKind::Site, $path),
        OutputPath::fromRelativePath(str_replace('.blade.php', '.html', $path)),
    );
}

function content(string $path, array $metadata = []): ContentPage
{
    return ContentPage::fromParsed(
        SourceLocation::fromRelativePath(SourceKind::Contents, $path),
        new ParsedContent(Metadata::fromArray($metadata), 'raw', '<p>html</p>'),
    );
}

/** @return list<string> */
function pathsOf(PageCollection $collection): array
{
    return array_map(
        fn($page) => $page->sourceLocation()->relativePath,
        array_values($collection->all()),
    );
}

it('counts the pages', function () {
    expect(count(new PageCollection([sitePage('about.blade.php')])))->toBe(1);
});

it('iterates the pages', function () {
    $collection = new PageCollection([
        sitePage('about.blade.php'),
        sitePage('contact.blade.php'),
    ]);

    expect(pathsOf(new PageCollection(iterator_to_array($collection))))
        ->toBe(['about.blade.php', 'contact.blade.php']);
});

it('returns the first page or null', function () {
    expect((new PageCollection([sitePage('about.blade.php')]))->first())->not->toBeNull()
        ->and((new PageCollection([]))->first())->toBeNull();
});

it('reads a page by key', function () {
    $page = sitePage('about.blade.php');

    expect((new PageCollection([$page]))[0])->toBe($page);
});

it('returns null for a missing key', function () {
    expect((new PageCollection([]))[5])->toBeNull();
});

it('removes drafts', function () {
    $collection = new PageCollection([
        content('blog/published.md'),
        content('blog/draft.md', ['draft' => true]),
    ]);

    expect($collection->withoutDrafts()->count())->toBe(1);
});

it('keeps pages that are not draftable', function () {
    expect((new PageCollection([sitePage('about.blade.php')]))->withoutDrafts()->count())->toBe(1);
});

it('returns only the pages with a tag', function () {
    $collection = new PageCollection([
        content('blog/php.md', ['tags' => ['php']]),
        content('blog/rust.md', ['tags' => ['rust']]),
    ]);

    expect($collection->tagged('php')->count())->toBe(1);
});

it('groups pages by the source directory prefix', function () {
    $collection = new PageCollection([
        content('blog/a.md'),
        content('notes/b.md'),
        content('blog/c.md'),
    ]);

    expect($collection->group('blog')->count())->toBe(2)
        ->and($collection->group('notes')->count())->toBe(1);
});

it('aliases group through property access', function () {
    expect((new PageCollection([content('blog/a.md')]))->blog->count())->toBe(1);
});

it('sorts the latest pages in descending order', function () {
    $collection = new PageCollection([
        content('blog/old.md', ['published' => '2023-01-01']),
        content('blog/newest.md', ['published' => '2025-01-01']),
        content('blog/middle.md', ['published' => '2024-01-01']),
    ]);

    expect(pathsOf($collection->latest(3)))->toBe([
        'blog/newest.md',
        'blog/middle.md',
        'blog/old.md',
    ]);
});

it('limits the latest pages to the given count', function () {
    $collection = new PageCollection([
        content('blog/a.md', ['published' => '2023-01-01']),
        content('blog/b.md', ['published' => '2024-01-01']),
        content('blog/c.md', ['published' => '2025-01-01']),
    ]);

    expect($collection->latest(2)->count())->toBe(2)
        ->and($collection->latest(1)->count())->toBe(1)
        ->and($collection->latest()->count())->toBe(3);
});

it('defaults the latest count to five', function () {
    $pages = [];

    for ($i = 1; $i <= 7; $i++) {
        $pages[] = content("blog/post-{$i}.md", ['published' => sprintf('2020-01-%02d', $i)]);
    }

    expect((new PageCollection($pages))->latest()->count())->toBe(5);
});

it('excludes undated pages from latest', function () {
    $collection = new PageCollection([
        content('blog/dated.md', ['published' => '2024-01-01']),
        content('blog/undated.md'),
    ]);

    expect($collection->latest()->count())->toBe(1);
});

it('excludes drafts from latest', function () {
    $collection = new PageCollection([
        content('blog/published.md', ['published' => '2024-01-01']),
        content('blog/draft.md', ['published' => '2025-01-01', 'draft' => true]),
    ]);

    expect($collection->latest()->count())->toBe(1);
});

it('excludes pages that are not publication dated from latest', function () {
    $collection = new PageCollection([
        content('blog/dated.md', ['published' => '2024-01-01']),
        sitePage('about.blade.php'),
    ]);

    expect($collection->latest()->count())->toBe(1);
});

it('rejects writing through offsetSet', function () {
    $collection = new PageCollection([]);

    $collection[0] = sitePage('about.blade.php');
})->throws(BadMethodCallException::class, 'PageCollection is immutable');

it('rejects writing through offsetUnset', function () {
    $collection = new PageCollection([sitePage('about.blade.php')]);

    unset($collection[0]);
})->throws(BadMethodCallException::class, 'PageCollection is immutable');
