<?php

namespace Blougly\Domain\ValueObjects;

use Stringable;

final class Slug implements Stringable
{
    private function __construct(
        public readonly string $value
    ) {}

    public static function fromText(string $input): self
    {
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $input);

        if ($slug === false) {
            throw new \InvalidArgumentException("Unable to generate a slug from the given text: {$input}");
        }

        $slug = strtolower($slug);
        $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);

        if ($slug === null) {
            throw new \InvalidArgumentException("Unable to generate a slug from the given text: {$input}");
        }

        $slug = trim($slug, '-');

        if ($slug === '') {
            throw new \InvalidArgumentException("The given text does not produce a valid slug: {$input}");
        }

        return new self($slug);
    }

    public static function fromExplicit(string $input): self
    {
        $expectedSlug = self::fromText($input)->value;

        if ($input !== $expectedSlug) {
            throw new \InvalidArgumentException(sprintf(
                'The slug "%s" is invalid. Expected format: "%s".',
                $input,
                $expectedSlug
            ));
        }

        return new self($input);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
