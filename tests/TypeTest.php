<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SortedLinkedList\Type;

class TypeTest extends TestCase
{
    public static function correctTypeDataProvider(): array
    {
        return [
            [Type::INTEGER, 1, true],
            [Type::INTEGER, '1', false],
            [Type::STRING, '1', true],
            [Type::STRING, 1, false],
        ];
    }

    #[DataProvider('correctTypeDataProvider')]
    public function testIsCorrectType(Type $type, mixed $input, bool $expected): void
    {
        self::assertSame($expected, $type->isCorrectType($input));
    }

    public static function fromValueDataProvider(): array
    {
        return [
            [1, Type::INTEGER, false],
            ['abc', Type::STRING, false],
            [1.558, null, true],
        ];
    }

    #[DataProvider('fromValueDataProvider')]
    public function testFromValue(mixed $value, ?Type $expected, bool $expectedException): void
    {
        if ($expectedException) {
            self::expectException(\InvalidArgumentException::class);
        }

        self::assertSame($expected, Type::fromValue($value));
    }
}
