<?php

declare(strict_types=1);

namespace SortedLinkedList;

use Countable;
use InvalidArgumentException;
use Iterator;
use OutOfBoundsException;

/**
 * @implements Iterator<int, int|string>
 */
class SortedLinkedList implements Iterator, Countable
{
    private ?Node $head = null;

    private int $position = 0;

    private int $size = 0;

    private ?Node $currentItem = null;

    private Type $type;

    /**
     * @param int[]|string[] $elements
     * @throws InvalidArgumentException
     */
    public function __construct(
        array $elements,
        ?Type $type = null,
        private readonly SortDirection $direction = SortDirection::ASC,
    ) {
        $this->type = $type ?? Type::fromValue(reset($elements));

        foreach ($elements as $element) {
            $this->add($element);
        }
    }

    public function add(int|string $element): void
    {
        $this->validateElementType($element);

        // if empty list
        if ($this->head === null) {
            $this->head = new Node($element);
        }
        // element should be the new head
        elseif ($this->shouldBeNewHead($this->head->value, $element)) {
            $oldHead = $this->head;
            $this->head = new Node($element);
            $this->head->next = $oldHead;
        }
        // element should be inserted somewhere in the list
        else {
            $elementAdded = false;
            $current = $this->head;
            $previous = null;

            while ($this->shouldContinueTraversal($current->value, $element)) {
                // Skip if element already exists
                if ($current->value === $element) {
                    $this->rewind();
                    return;
                }

                // If we reached the end of the list, we need to add an element at the end
                if ($current->next === null) {
                    $current->next = new Node($element);
                    $elementAdded = true;
                    break;
                }

                $previous = $current;
                $current = $current->next;
            }

            // If we didn't add the element yet and there was no duplicate,
            // insert it between previous and current
            if (!$elementAdded) {
                $newNode = new Node($element);
                $previous->next = $newNode;
                $newNode->next = $current;
            }
        }

        //increase the size
        $this->size++;
        //reset the iterator
        $this->rewind();
    }

    private function shouldBeNewHead(mixed $headValue, mixed $newElement): bool
    {
        return match ($this->direction) {
            SortDirection::ASC => $headValue > $newElement,
            SortDirection::DESC => $headValue < $newElement,
        };
    }

    private function shouldContinueTraversal(mixed $currentValue, mixed $newElement): bool
    {
        return match ($this->direction) {
            SortDirection::ASC => $currentValue <= $newElement,
            SortDirection::DESC => $currentValue >= $newElement,
        };
    }

    public function remove(mixed $element): void
    {
        $this->validateElementType($element);

        $current = $this->head;
        $previous = null;

        //iterate over the list
        while ($current !== null) {
            //if we found the element, remove it and connect previous and next
            if ($current->value === $element) {
                $next = $current->next;
                if ($previous !== null) {
                    $previous->next = $next;
                } else {
                    $this->head = $next;
                }
                //free memory
                unset($current);
                //decrease the size
                $this->size--;
                //reset the iterator
                $this->rewind();
                return;
            }
            $previous = $current;
            $current = $current->next;
        }

        //reset the iterator even if we didn't find the element
        $this->rewind();
    }


    /**
     * @throws OutOfBoundsException
     */
    public function current(): mixed
    {
        /** @phpstan-ignore return.type */
        return $this->currentItem->value ?? throw new OutOfBoundsException();
    }

    public function next(): void
    {
        $this->currentItem = $this->currentItem->next;
        $this->position++;
    }

    public function key(): ?int
    {
        return $this->size === 0 ? null : $this->position;
    }

    public function valid(): bool
    {
        return $this->currentItem !== null;
    }

    public function rewind(): void
    {
        $this->position = 0;
        $this->currentItem = $this->head;
    }

    public function count(): int
    {
        /** @phpstan-ignore return.type */
        return $this->size;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function validateElementType(mixed $element): void
    {
        if (!$this->type->isCorrectType($element)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Element type is not correct, expected: %s, got: %s',
                    $this->type->value,
                    gettype($element),
                )
            );
        }
    }
}
