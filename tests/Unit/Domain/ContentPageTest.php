<?php

use Blougly\Domain\Contents\ParsedContent;
use Blougly\Domain\Pages\ContentPage;
use Blougly\Domain\ValueObjects\Metadata;
use Blougly\Domain\ValueObjects\SourceKind;
use Blougly\Domain\ValueObjects\SourceLocation;

function contentPage(string $path, array $metadata = []): ContentPage
{
    return ContentPage::fromParsed(
        SourceLocation::fromRelativePath(SourceKind::Contents, $path),
        new ParsedContent(Metadata::fromArray($metadata), 'raw body', '<p>html body</p>'),
    );
}

it('nests the output path under the year when published is present', function () {
    $page = contentPage('blog/hello.md', ['published' => '2024-05-01']);

    expect($page->outputPath()->relativePath)->toBe('blog/2024/hello.html')
        ->and($page->publishedAt()?->format('Y-m-d'))->toBe('2024-05-01');
});

it('omits the year when published is absent', function () {
    expect(contentPage('blog/hello.md')->outputPath()->relativePath)
        ->toBe('blog/hello.html');
});

it('builds the output path at the root when the file has no directory', function () {
    expect(contentPage('hello.md')->outputPath()->relativePath)
        ->toBe('hello.html');
});

it('builds the output path at the root keeping the year', function () {
    expect(contentPage('hello.md', ['published' => '2024-05-01'])->outputPath()->relativePath)
        ->toBe('2024/hello.html');
});

it('slugifies the directory part of the output path', function () {
    expect(contentPage('My Notes/My Post.md')->outputPath()->relativePath)
        ->toBe('my-notes/my-post.html');
});

it('falls back to the title for the slug', function () {
    expect(contentPage('blog/hello.md', ['title' => 'Hello There'])->outputPath()->relativePath)
        ->toBe('blog/hello-there.html');
});

it('falls back to the file basename when no title is given', function () {
    expect(contentPage('blog/hello-world.md')->outputPath()->relativePath)
        ->toBe('blog/hello-world.html');
});

it('accepts an explicit canonical slug', function () {
    $page = contentPage('blog/hello.md', [
        'title' => 'Hello There',
        'slug' => 'custom-slug',
    ]);

    expect($page->outputPath()->relativePath)->toBe('blog/custom-slug.html');
});

it('rejects a non canonical explicit slug', function () {
    contentPage('blog/hello.md', ['slug' => 'Custom Slug']);
})->throws(InvalidArgumentException::class, 'is invalid. Expected format');

it('accepts the unix timestamp format for published', function () {
    $page = contentPage('blog/hello.md', ['published' => '1714521600']);

    expect($page->publishedAt()?->format('Y-m-d'))->toBe('2024-05-01');
});

it('accepts the datetime format for published', function () {
    $page = contentPage('blog/hello.md', ['published' => '2024-05-01 10:30:00']);

    expect($page->publishedAt()?->format('Y-m-d H:i:s'))->toBe('2024-05-01 10:30:00');
});

it('accepts the atom format for published', function () {
    $page = contentPage('blog/hello.md', ['published' => '2024-05-01T10:30:00+00:00']);

    expect($page->publishedAt()?->format('Y-m-d H:i:s'))->toBe('2024-05-01 10:30:00');
});

it('rejects an unparseable published date', function () {
    contentPage('blog/hello.md', ['published' => 'not-a-date']);
})->throws(InvalidArgumentException::class, "Invalid 'published' in blog/hello.md");

it('rejects a non string published date', function () {
    contentPage('blog/hello.md', ['published' => true]);
})->throws(InvalidArgumentException::class, "Invalid 'published' in blog/hello.md");

it('rejects a non boolean draft', function () {
    contentPage('blog/hello.md', ['draft' => 'yes']);
})->throws(InvalidArgumentException::class, "Invalid 'draft' in blog/hello.md");

it('is a draft when draft is true', function () {
    expect(contentPage('blog/hello.md', ['draft' => true])->isDraft())->toBeTrue();
});

it('is not a draft by default', function () {
    expect(contentPage('blog/hello.md')->isDraft())->toBeFalse();
});

it('rejects an empty template', function () {
    contentPage('blog/hello.md', ['template' => '']);
})->throws(RuntimeException::class, "Missing or invalid 'template' in blog/hello.md");

it('rejects a non string template', function () {
    contentPage('blog/hello.md', ['template' => 42]);
})->throws(RuntimeException::class, "Missing or invalid 'template' in blog/hello.md");

it('keeps the template name', function () {
    expect(contentPage('blog/hello.md', ['template' => 'post'])->templateName())
        ->toBe('post');
});

it('has no template when the key is absent', function () {
    expect(contentPage('blog/hello.md')->templateName())->toBeNull();
});

it('matches tags strictly', function () {
    $page = contentPage('blog/hello.md', ['tags' => ['php', 'blougly']]);

    expect($page->hasTag('php'))->toBeTrue()
        ->and($page->hasTag('blougly'))->toBeTrue()
        ->and($page->hasTag('PHP'))->toBeFalse();
});

it('has no tags when the key is absent', function () {
    expect(contentPage('blog/hello.md')->hasTag('php'))->toBeFalse();
});

it('exposes metadata merged with the html body for templates', function () {
    $page = contentPage('blog/hello.md', ['title' => 'Hello']);

    expect($page->templateData())->toBe([
        'title' => 'Hello',
        'body' => '<p>html body</p>',
    ]);
});

it('keeps the raw and html bodies apart', function () {
    $page = contentPage('blog/hello.md');

    expect($page->rawBody())->toBe('raw body')
        ->and($page->htmlBody())->toBe('<p>html body</p>');
});

it('parses created and updated dates', function () {
    $page = contentPage('blog/hello.md', [
        'created' => '2024-01-01',
        'updated' => '2024-02-02',
    ]);

    expect($page->createdAt()?->format('Y-m-d'))->toBe('2024-01-01')
        ->and($page->updatedAt()?->format('Y-m-d'))->toBe('2024-02-02');
});

it('leaves created, updated and published null when absent', function () {
    $page = contentPage('blog/hello.md');

    expect($page->createdAt())->toBeNull()
        ->and($page->updatedAt())->toBeNull()
        ->and($page->publishedAt())->toBeNull();
});
