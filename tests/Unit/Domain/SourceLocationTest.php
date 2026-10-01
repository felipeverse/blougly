<?php

use Blougly\Domain\ValueObjects\SourceKind;
use Blougly\Domain\ValueObjects\SourceLocation;

it('keeps the source kind and the relative path', function () {
    $location = SourceLocation::fromRelativePath(SourceKind::Contents, 'blog/hello.md');

    expect($location->sourceKind)->toBe(SourceKind::Contents)
        ->and($location->relativePath)->toBe('blog/hello.md');
});

it('normalizes backslashes to forward slashes', function () {
    expect(SourceLocation::fromRelativePath(SourceKind::Contents, 'blog\\hello.md')->relativePath)
        ->toBe('blog/hello.md');
});

it('rejects an empty path', function () {
    SourceLocation::fromRelativePath(SourceKind::Contents, '');
})->throws(InvalidArgumentException::class, 'cannot be empty');

it('rejects a unix absolute path', function () {
    SourceLocation::fromRelativePath(SourceKind::Contents, '/etc/passwd');
})->throws(InvalidArgumentException::class, 'must be relative');

it('rejects a windows absolute path', function () {
    SourceLocation::fromRelativePath(SourceKind::Data, 'C:\\data\\links.json');
})->throws(InvalidArgumentException::class, 'must be relative');

it('rejects a parent directory segment escaping the source dir', function () {
    SourceLocation::fromRelativePath(SourceKind::Contents, '../outside.md');
})->throws(InvalidArgumentException::class, 'Invalid segment');

it('rejects a dot segment', function () {
    SourceLocation::fromRelativePath(SourceKind::Site, './about.blade.php');
})->throws(InvalidArgumentException::class, 'Invalid segment');

it('rejects a double slash', function () {
    SourceLocation::fromRelativePath(SourceKind::Assets, 'css//style.css');
})->throws(InvalidArgumentException::class, 'Invalid segment');
