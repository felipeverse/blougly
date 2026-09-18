<?php

namespace Blougly\Domain\ValueObjects;

final class SiteConfig
{
    private function __construct(private readonly array $values) {}

    public static function fromArray(array $values): self
    {
        if ($values !== [] && array_is_list($values)) {
            throw new \InvalidArgumentException('Site config must be a map of key => value, not a list.');
        }

        return new self($values);
    }

    public static function empty(): self
    {
        return new self([]);
    }

    /** @return array<string, mixed> */
    public function all(): array
    {
        return $this->values;
    }
}
