<?php

namespace Blougly\Domain\Pages;

use Blougly\Domain\Pages\Contracts\Draftable;
use Blougly\Domain\Pages\Contracts\HasPublicationDate;
use Blougly\Domain\Pages\Contracts\Page;
use Blougly\Domain\Pages\Contracts\Taggable;
use Traversable;

final class PageCollection implements \Countable, \IteratorAggregate, \ArrayAccess
{
    public function __construct(
        /** @param Page[] $pages */
        private readonly array $pages,
    ) {}

    public function __get(string $group): self
    {
        return $this->group($group);
    }

    public function count(): int
    {
        return count($this->pages);
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->pages);
    }

    public function offsetExists(mixed $key): bool
    {
        return isset($this->pages[$key]);
    }

    public function offsetGet(mixed $key): mixed
    {
        return $this->pages[$key] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \BadMethodCallException('PageCollection is immutable');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new \BadMethodCallException('PageCollection is immutable');
    }

    public function all(): array
    {
        return $this->pages;
    }

    public function first(): ?Page
    {
        return $this->pages[0] ?? null;
    }

    public function latest(int $count = 5): self
    {
        $withDates = array_filter(
            $this->pages,
            fn(Page $item) => $item instanceof HasPublicationDate && $item->publishedAt() !== null
        );

        $withoutDrafts = (new self(array_values($withDates)))->withoutDrafts()->all();

        usort(
            $withoutDrafts,
            fn($a, $b) => $b->publishedAt() <=> $a->publishedAt()
        );

        return new self(array_slice($withoutDrafts, 0, $count));
    }

    public function tagged(string $tag): self
    {
        $filtered = array_filter(
            $this->pages,
            fn(Page $item) => $item instanceof Taggable && $item->hasTag($tag)
        );

        return new self(array_values($filtered));
    }

    public function group(string $name): self
    {
        return new self(array_values(array_filter(
            $this->pages,
            fn(Page $item) => str_starts_with($item->sourceLocation()->relativePath, "{$name}/")
        )));
    }

    public function withoutDrafts(): self
    {
        $filtered = array_filter(
            $this->pages,
            fn(Page $item) => !($item instanceof Draftable) || !$item->isDraft()
        );

        return new self(array_values($filtered));
    }
}
