<?php

namespace Blougly\Domain\ValueObjects;

use Stringable;

final class Slug implements Stringable
{
    /**
     * Accented latin letters replaced by their ascii base letter, applied
     * before the iconv transliteration because musl's iconv turns some of
     * them into symbols (ç to ~, á to ') instead of dropping the diacritic.
     */
    private const TRANSLITERATIONS = [
        'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'ä' => 'a', 'å' => 'a', 'ā' => 'a',
        'é' => 'e', 'è' => 'e', 'ẽ' => 'e', 'ê' => 'e', 'ë' => 'e', 'ē' => 'e',
        'í' => 'i', 'ì' => 'i', 'ĩ' => 'i', 'î' => 'i', 'ï' => 'i', 'ī' => 'i',
        'ó' => 'o', 'ò' => 'o', 'õ' => 'o', 'ô' => 'o', 'ö' => 'o', 'ō' => 'o',
        'ú' => 'u', 'ù' => 'u', 'ũ' => 'u', 'û' => 'u', 'ü' => 'u', 'ū' => 'u',
        'ç' => 'c', 'ñ' => 'n', 'ý' => 'y', 'ÿ' => 'y',
        'Á' => 'A', 'À' => 'A', 'Ã' => 'A', 'Â' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Ā' => 'A',
        'É' => 'E', 'È' => 'E', 'Ẽ' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ē' => 'E',
        'Í' => 'I', 'Ì' => 'I', 'Ĩ' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ī' => 'I',
        'Ó' => 'O', 'Ò' => 'O', 'Õ' => 'O', 'Ô' => 'O', 'Ö' => 'O', 'Ō' => 'O',
        'Ú' => 'U', 'Ù' => 'U', 'Ũ' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ū' => 'U',
        'Ç' => 'C', 'Ñ' => 'N', 'Ý' => 'Y',
    ];

    private function __construct(
        public readonly string $value
    ) {}

    public static function fromText(string $input): self
    {
        $input = strtr($input, self::TRANSLITERATIONS);

        $slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $input);

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
