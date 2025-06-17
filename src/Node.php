<?php

declare(strict_types=1);

namespace SortedLinkedList;

class Node
{
    public function __construct(
        public readonly mixed $value,
        public ?Node $next = null,
    ) {
    }
}
