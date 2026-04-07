<?php

declare(strict_types=1);

namespace App\Policy\Obligation;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 *
 */

/**
 *
 */
final class Obligations implements IteratorAggregate
{
    /** @var array */
    private array $items;

    /**
     *
     */
    private function __construct()
    {
        $items = [];
        $this->items = $items;
    }

    public static function empty(): self
    {
        return new self();
    }

    /**
     * @param \Policy\Role\Obligation\Obligation $o
     * @return self
     */
    public function with(Obligation $o): self
    {
        $c = clone $this;
        $c->items[] = $o;
        return $c;
    }

    /** @return \Traversable<int,Obligation> */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function add(Obligation $o): void
    {
        $this->items[] = $o;
    }

    public function isEmpty(): bool
    {
        return $this->items === [];
    }

    /** @return list<Obligation> */
    public function all(): array
    {
        return $this->items;
    }
}
