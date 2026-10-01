<?php

use Blougly\Domain\ValueObjects\SiteConfig;

it('accepts a map of key => value', function () {
    $config = SiteConfig::fromArray([
        'title' => 'Blougly',
        'description' => 'An ugly static site generator',
    ]);

    expect($config->all())->toBe([
        'title' => 'Blougly',
        'description' => 'An ugly static site generator',
    ]);
});

it('rejects a list', function () {
    SiteConfig::fromArray(['a', 'b']);
})->throws(InvalidArgumentException::class, 'must be a map of key => value, not a list');

it('rejects a single item list', function () {
    SiteConfig::fromArray(['a']);
})->throws(InvalidArgumentException::class, 'must be a map of key => value, not a list');

it('accepts an empty array', function () {
    expect(SiteConfig::fromArray([])->all())->toBe([]);
});

it('builds an empty config', function () {
    expect(SiteConfig::empty()->all())->toBe([]);
});

it('preserves nested values', function () {
    $config = SiteConfig::fromArray([
        'author' => ['name' => 'Felipe', 'social' => ['github' => 'felipeverse']],
    ]);

    expect($config->all()['author']['social']['github'])->toBe('felipeverse');
});
