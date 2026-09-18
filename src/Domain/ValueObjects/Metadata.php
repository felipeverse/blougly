<?php

namespace Blougly\Domain\ValueObjects;

final class Metadata
{
    private function __construct(private array $metadata) {}

    public static function fromArray(array $metadata): self
    {
        return new self($metadata);
    }

    public static function empty(): self
    {
        return new self([]);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->metadata[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return \array_key_exists($key, $this->metadata);
    }

    public function all(): array
    {
        return $this->metadata;
    }
}
