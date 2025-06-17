# PHP Sorted Linked List 

![Static Badge](https://img.shields.io/badge/PHP_Version-%3E%3D8.4-blue)


The **Sorted Linked List** is library providing a sorted linked list functionality. 

## Requirements
* PHP version >= 8.4

## Installation

You can install the **Sorted Linked List** using the package manager [composer](https://getcomposer.org/).

```bash
  composer require krooyh/sorted-linked-list
```

## Usage

> Sorted Linked List accepts ints and strings. By default, the list will be sorted in ascending order but you can specify the direction.
> 
> If you provide an array of elements in the constructor, and you won't specify a type, the type will be inferred from the first element or exception will be thrown if the type is not supported. 
> 
> If you provide a type in the constructor, the list will be stored in that type.

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use SortedLinkedList\SortedLinkedList;

//you can create an instance of SortedLinkedList with an array
$sortedLinkedList = new SortedLinkedList(elements: [1, 8, 3, 5]);

//you can create an instance of SortedLinkedList with a type
$sortedLinkedList = new SortedLinkedList(type: Type::STRING);

//you can create an instance of SortedLinkedList with a type and direction
$sortedLinkedList = new SortedLinkedList(
    elements:[1, 8, 3, 5],
    type: ::INT,
    direction: SortDirection::DESC
);

//you can add an element to the list
$sortedLinkedList->add(8);

//you can remove an element from the list
$sortedLinkedList->remove(3);

//you can iterate over the list
foreach ($sortedLinkedList as $element) {
    echo $element;
}

//you can get the size of the list
$sortedLinkedList->count();

//this will throw an exception because of type mismatch
$sortedLinkedList = new SortedLinkedList(elements: [1, 8, 3, 5], type: Type::STRING);
```