<?php

namespace Blougly\Domain\Data;

use Traversable;

final class DataCollection implements \Countable, \IteratorAggregate, \ArrayAccess
{
    public function __construct(
        /** @param array<string, DataSet> $entries */
        private readonly array $entries
    ) {}

    public function count(): int
    {
        return count($this->entries);
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->entries);
    }

    public function offsetExists(mixed $key): bool
    {
        return isset($this->entries[$key]);
    }

    public function offsetGet(mixed $key): mixed
    {
        return $this->entries[$key] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \BadMethodCallException('DataCollection is immutable');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new \BadMethodCallException('DataCollection is immutable');
    }

    public function all(): array
    {
        return $this->entries;
    }

    public static function fromDataSets(iterable $dataSets): self
    {
        $items = [];

        /** @var array<DataSet> $dataSets */
        foreach ($dataSets as $dataSet) {
            $key = pathinfo(basename($dataSet->sourceLocation()->relativePath), PATHINFO_FILENAME);
            $items[$key] = $dataSet;
        }

        return new self($items);
    }
}
