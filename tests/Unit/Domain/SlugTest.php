<?php

use Blougly\Domain\ValueObjects\Slug;

it('lowercases and hyphenates text', function () {
    expect(Slug::fromText('Hello World')->value)->toBe('hello-world');
});

it('collapses runs of separators into a single hyphen', function () {
    expect(Slug::fromText('Hello---World')->value)->toBe('hello-world')
        ->and(Slug::fromText('  spaced   out  ')->value)->toBe('spaced-out')
        ->and(Slug::fromText('a/b/c')->value)->toBe('a-b-c');
});

it('strips leading and trailing separators', function () {
    expect(Slug::fromText('---edge---')->value)->toBe('edge');
});

it('transliterates accents to ascii', function () {
    expect(Slug::fromText('Ação')->value)->toBe('acao')
        ->and(Slug::fromText('Corações')->value)->toBe('coracoes')
        ->and(Slug::fromText('Olá Mundo')->value)->toBe('ola-mundo');
});

it('keeps digits', function () {
    expect(Slug::fromText('Top 10 Motivos')->value)->toBe('top-10-motivos');
});

it('throws when the text produces an empty slug', function () {
    Slug::fromText('...');
})->throws(InvalidArgumentException::class, 'does not produce a valid slug');

it('accepts an already canonical slug in fromExplicit', function () {
    $slug = Slug::fromExplicit('hello-world');

    expect($slug->value)->toBe('hello-world');
});

it('rejects a non canonical slug in fromExplicit', function () {
    Slug::fromExplicit('Hello World');
})->throws(InvalidArgumentException::class, 'is invalid. Expected format');

it('rejects a non canonical slug with accents in fromExplicit', function () {
    Slug::fromExplicit('olá-mundo');
})->throws(InvalidArgumentException::class, 'is invalid. Expected format');

it('casts to string', function () {
    expect((string) Slug::fromText('Hello World'))->toBe('hello-world');
});
