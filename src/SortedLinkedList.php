<?php

declare(strict_types=1);

namespace SortedLinkedList;

use Countable;
use InvalidArgumentException;
use Iterator;
use OutOfBoundsException as OutOfBoundsExceptionAlias;

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
    ) {
        $this->type = $type ?? Type::fromValue(reset($elements));

        foreach ($elements as $element) {
            $this->add($element);
        }
    }

    public function add(int|string $element): void
    {
        $this->validateElementType($element);

        if ($this->head === null) {
            $this->head = new Node($element);
            $this->size++;
            $this->rewind();
            return;
        }

        if ($this->head->value > $element) {
            $oldHead = $this->head;
            $this->head = new Node($element);
            $this->head->next = $oldHead;
            $this->size++;
            $this->rewind();
            return;
        }

        $current = $this->head;
        $previous = null;

        while ($current->value <= $element) {
            if ($current->value === $element) {
                $this->rewind();
                return;
            }

            if ($current->next === null) {
                $current->next = new Node($element);
                $this->size++;
                $this->rewind();
                return;
            }

            $previous = $current;
            $current = $current->next;
        }

        $newNode = new Node($element);
        $previous->next = $newNode;
        $previous->next->next = $current;
        $this->size++;
        $this->rewind();
    }

    public function remove(mixed $element): void
    {
        $this->validateElementType($element);

        $current = $this->head;
        $previous = null;

        while ($current !== null) {
            if ($current->value === $element) {
                $next = $current->next;
                if ($previous !== null) {
                    $previous->next = $next;
                } else {
                    $this->head = $next;
                }
                unset($current);
                $this->size--;
                $this->rewind();
                return;
            }
            $previous = $current;
            $current = $current->next;
        }

        $this->rewind();
    }


    /**
     * @throws OutOfBoundsExceptionAlias
     */
    public function current(): mixed
    {
        /** @phpstan-ignore return.type */
        return $this->currentItem->value ?? throw new OutOfBoundsExceptionAlias();
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
