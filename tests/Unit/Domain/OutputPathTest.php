<?php

use Blougly\Domain\ValueObjects\OutputPath;

it('accepts a valid relative path', function () {
    expect(OutputPath::fromRelativePath('blog/hello.html')->relativePath)
        ->toBe('blog/hello.html');
});

it('accepts a single segment relative path', function () {
    expect(OutputPath::fromRelativePath('index.html')->relativePath)
        ->toBe('index.html');
});

it('normalizes backslashes to forward slashes', function () {
    expect(OutputPath::fromRelativePath('blog\\hello.html')->relativePath)
        ->toBe('blog/hello.html');
});

it('rejects an empty path', function () {
    OutputPath::fromRelativePath('');
})->throws(InvalidArgumentException::class, 'cannot be empty');

it('rejects a unix absolute path', function () {
    OutputPath::fromRelativePath('/var/www/index.html');
})->throws(InvalidArgumentException::class, 'must be relative');

it('rejects a windows absolute path', function () {
    OutputPath::fromRelativePath('C:\\www\\index.html');
})->throws(InvalidArgumentException::class, 'must be relative');

it('rejects a windows absolute path with forward slashes', function () {
    OutputPath::fromRelativePath('C:/www/index.html');
})->throws(InvalidArgumentException::class, 'must be relative');

it('rejects a leading backslash', function () {
    OutputPath::fromRelativePath('\\www\\index.html');
})->throws(InvalidArgumentException::class, 'must be relative');

it('rejects a dot segment', function () {
    OutputPath::fromRelativePath('blog/./hello.html');
})->throws(InvalidArgumentException::class, 'Invalid segment');

it('rejects a parent directory segment', function () {
    OutputPath::fromRelativePath('blog/../hello.html');
})->throws(InvalidArgumentException::class, 'Invalid segment');

it('rejects a double slash', function () {
    OutputPath::fromRelativePath('blog//hello.html');
})->throws(InvalidArgumentException::class, 'Invalid segment');

it('rejects a trailing slash', function () {
    OutputPath::fromRelativePath('blog/');
})->throws(InvalidArgumentException::class, 'Invalid segment');
