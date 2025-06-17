<?php
declare(strict_types=1);

namespace SortedLinkedListTests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SortedLinkedList\SortedLinkedList;
use SortedLinkedList\SortDirection;
use SortedLinkedList\Type;

class SortedLinkedListTest extends TestCase
{
    public static function typeDataProvider(): array
    {
        return [
            [Type::INTEGER],
            [Type::STRING],
        ];
    }

    #[DataProvider('typeDataProvider')]
    public function testEmptyList(Type $type): void
    {
        $sortedLinkedList = new SortedLinkedList([], $type);

        self::assertFalse($sortedLinkedList->valid());
        self::assertEquals(0, $sortedLinkedList->count());
        self::assertNull($sortedLinkedList->key());
        self::expectException(\OutOfBoundsException::class);
        $sortedLinkedList->current();
    }

    public function testEmptyListWithoutType(): void
    {
        self::expectException(\InvalidArgumentException::class);
        new SortedLinkedList([]);
    }

    public static function wrongTypeOperationsDataProvider(): array
    {
        return [
            [Type::INTEGER, 'value'],
            [Type::STRING, 0],
        ];
    }

    #[DataProvider('wrongTypeOperationsDataProvider')]
    public function testWrongTypeOperations(Type $type, mixed $element): void
    {
        self::expectException(\InvalidArgumentException::class);
        new SortedLinkedList([$element], $type);
    }

    public static function elementsDataProvider(): array
    {
        return [
            'int duplicated elements' => [[1, 1], [1]],
            'int duplicated elements with different order' => [[23, 1, 7, 1], [1, 7, 23]],
            'int ordered elements' => [[1, 2, 3], [1, 2, 3]],
            'int head change' => [[10, 2, 3], [2, 3, 10]],
            'int some case' => [[1, 8, 2, 4, 3], [1, 2, 3, 4, 8]],
            'string ordered elements' => [['a', 'b', 'c'], ['a', 'b', 'c']],
            'string some case' => [['a', 'f', 'c'], ['a', 'c', 'f']],
            'string head change' => [['7', '6', '9'], ['6', '7', '9']],
        ];
    }

    #[DataProvider('elementsDataProvider')]
    public function testListConstructorElements(array $input, array $expected): void
    {
        $sortedLinkedList = new SortedLinkedList($input);

        self::assertEquals($expected, $this->sllToArray($sortedLinkedList));
        self::assertEquals(count($expected), $sortedLinkedList->count());
    }

    private function sllToArray(SortedLinkedList $sortedLinkedList): array
    {
        $result = [];
        $sortedLinkedList->rewind();
        while ($sortedLinkedList->valid()) {
            $result[] = $sortedLinkedList->current();
            $sortedLinkedList->next();
        }
        return $result;
    }

    #[DataProvider('elementsDataProvider')]
    public function testAdd(array $input, array $expected): void
    {
        $sortedLinkedList = new SortedLinkedList([], Type::fromValue($input[0]));

        foreach ($input as $element) {
            $sortedLinkedList->add($element);
        }

        self::assertEquals($expected, $this->sllToArray($sortedLinkedList));
        self::assertEquals(count($expected), $sortedLinkedList->count());
    }

    public static function removeCasesDataProvider(): array
    {
        return [
            'int few reductions' => [[5, 9, 2, 115], [2, 9], [5, 115]],
            'int empty at the end' => [[9, 5], [5, 9], []],
            'int head change' => [[9, 5], [5], [9]],
            'int remove not found' => [[9, 5], [1], [5, 9]],
            'int remove not found on empty' => [[9, 5], [5, 9, 11], []],
            'string few reductions' => [['b', 'f', 'o', 'a'], ['a', 'f'], ['b', 'o']],
            'string empty at the end' => [['b', 'a'], ['a', 'b'], []],
            'string head change' => [['b', 'a'], ['a'], ['b']],
            'string remove not found' => [['b', 'a'], ['r'], ['a', 'b']],
            'string remove not found on empty' => [['a'], ['a', 'f'], []],
        ];
    }

    #[DataProvider('removeCasesDataProvider')]
    public function testRemove(array $given, array $toRemove, array $expected): void
    {
        $sortedLinkedList = new SortedLinkedList($given);

        foreach ($toRemove as $element) {
            $sortedLinkedList->remove($element);
        }

        self::assertEquals($expected, $this->sllToArray($sortedLinkedList));
        self::assertEquals(count($expected), $sortedLinkedList->count());
    }

    public function testIteration(): void
    {
        $sortedLinkedList = new SortedLinkedList([9, 2, 3, 5]);

        $result = [];

        foreach ($sortedLinkedList as $key => $element) {
            $result[$key] = $element;
        }

        self::assertEquals([0 => 2, 1 => 3, 2 => 5, 3 => 9], $result);
    }

    public static function descendingElementsDataProvider(): array
    {
        return [
            'int duplicated elements' => [[1, 1], [1]],
            'int duplicated elements with different order' => [[23, 1, 7, 1], [23, 7, 1]],
            'int ordered elements' => [[1, 2, 3], [3, 2, 1]],
            'int head change' => [[10, 2, 3], [10, 3, 2]],
            'int some case' => [[1, 8, 2, 4, 3], [8, 4, 3, 2, 1]],
            'string ordered elements' => [['a', 'b', 'c'], ['c', 'b', 'a']],
            'string some case' => [['a', 'f', 'c'], ['f', 'c', 'a']],
            'string head change' => [['7', '6', '9'], ['9', '7', '6']],
        ];
    }


    #[DataProvider('descendingElementsDataProvider')]
    public function testListConstructorElementsDescending(array $input, array $expected): void
    {
        $sortedLinkedList = new SortedLinkedList(elements: $input, direction: SortDirection::DESC);

        self::assertEquals($expected, $this->sllToArray($sortedLinkedList));
        self::assertEquals(count($expected), $sortedLinkedList->count());
    }

    #[DataProvider('descendingElementsDataProvider')]
    public function testAddSortDirectionDescending(array $input, array $expected): void
    {
        $sortedLinkedList = new SortedLinkedList(
            elements: [],
            type: Type::fromValue($input[0]),
            direction: SortDirection::DESC,
        );

        foreach ($input as $element) {
            $sortedLinkedList->add($element);
        }

        self::assertEquals($expected, $this->sllToArray($sortedLinkedList));
        self::assertEquals(count($expected), $sortedLinkedList->count());
    }

    public static function descendingRemoveCasesDataProvider(): array
    {
        return [
            'int few reductions' => [[5, 9, 2, 115], [2, 9], [115, 5]],
            'int empty at the end' => [[9, 5], [5, 9], []],
            'int head change' => [[9, 5], [9], [5]],
            'int remove not found' => [[9, 5], [1], [9, 5]],
            'int remove not found on empty' => [[9, 5], [5, 9, 11], []],
            'string few reductions' => [['b', 'f', 'o', 'a'], ['a', 'f'], ['o', 'b']],
            'string empty at the end' => [['b', 'a'], ['a', 'b'], []],
            'string head change' => [['b', 'a'], ['b'], ['a']],
            'string remove not found' => [['b', 'a'], ['r'], ['b', 'a']],
            'string remove not found on empty' => [['a'], ['a', 'f'], []],
        ];
    }

    #[DataProvider('descendingRemoveCasesDataProvider')]
    public function testRemoveSortDirectionDescending(array $given, array $toRemove, array $expected): void
    {
        $sortedLinkedList = new SortedLinkedList(elements: $given, direction: SortDirection::DESC);

        foreach ($toRemove as $element) {
            $sortedLinkedList->remove($element);
        }

        self::assertEquals($expected, $this->sllToArray($sortedLinkedList));
        self::assertEquals(count($expected), $sortedLinkedList->count());
    }

    public function testIterationSortDirectionDescending(): void
    {
        $sortedLinkedList = new SortedLinkedList(elements: [9, 2, 3, 5], direction: SortDirection::DESC);

        $result = [];

        foreach ($sortedLinkedList as $key => $element) {
            $result[$key] = $element;
        }

        self::assertEquals([0 => 9, 1 => 5, 2 => 3, 3 => 2], $result);
    }
}
