<?php

declare(strict_types=1);

namespace SortedLinkedList;

enum Type: string
{
    case INTEGER = 'integer';
    case STRING = 'string';

    public function isCorrectType(mixed $value): bool
    {
        return match ($this) {
            self::INTEGER => is_int($value),
            self::STRING => is_string($value),
        };
    }

    public static function fromValue(mixed $value): Type
    {
        return match (true) {
            is_int($value) => self::INTEGER,
            is_string($value) => self::STRING,
            default => throw new \InvalidArgumentException('Type is not supported'),
        };
    }
}
