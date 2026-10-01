<?php

use Blougly\Domain\Data\DataCollection;
use Blougly\Domain\Data\DataSet;
use Blougly\Domain\ValueObjects\OutputPath;
use Blougly\Domain\ValueObjects\SourceKind;
use Blougly\Domain\ValueObjects\SourceLocation;

function jsonFile(string $path, array $values = []): DataSet
{
    return new DataSet(
        SourceLocation::fromRelativePath(SourceKind::Data, $path),
        OutputPath::fromRelativePath(str_replace('.json', '.html', $path)),
        $values,
    );
}

it('keys entries by the file name without extension', function () {
    $links = jsonFile('links.json', ['github' => 'felipeverse']);
    $projects = jsonFile('projects.json', ['php' => 'Blougly']);

    $collection = DataCollection::fromDataSets([$links, $projects]);

    expect($collection['links'])->toBe($links)
        ->and($collection['projects'])->toBe($projects);
});

it('counts the entries', function () {
    $collection = DataCollection::fromDataSets([
        jsonFile('links.json'),
        jsonFile('projects.json'),
    ]);

    expect($collection->count())->toBe(2);
});

it('ignores the directory when keying entries', function () {
    $collection = DataCollection::fromDataSets([jsonFile('nested/links.json')]);

    expect($collection['links'])->not->toBeNull();
});

it('returns null for a missing key', function () {
    expect(DataCollection::fromDataSets([])['missing'])->toBeNull();
});

it('builds an empty collection from an empty iterable', function () {
    expect(DataCollection::fromDataSets([])->all())->toBe([]);
});

it('iterates the entries', function () {
    $collection = DataCollection::fromDataSets([
        jsonFile('links.json'),
        jsonFile('projects.json'),
    ]);

    expect(array_keys(iterator_to_array($collection)))->toBe(['links', 'projects']);
});

it('exposes the values of a data set', function () {
    $collection = DataCollection::fromDataSets([
        jsonFile('links.json', ['github' => 'felipeverse', 'x' => 'felipeverse_x']),
    ]);

    expect($collection['links']->all())->toBe([
        'github' => 'felipeverse',
        'x' => 'felipeverse_x',
    ])
        ->and($collection['links']->get('github'))->toBe('felipeverse')
        ->and($collection['links']->get('missing', 'fallback'))->toBe('fallback');
});
