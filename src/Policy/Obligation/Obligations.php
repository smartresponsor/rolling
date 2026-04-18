<?php

declare(strict_types=1);

namespace App\Policy\Obligation;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

final class Obligations implements IteratorAggregate
{
    /** @var list<Obligation> */
    private array $items;

    /** @param list<Obligation> $items */
    private function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public static function empty(): self
    {
        return new self();
    }

    public function with(Obligation $obligation): self
    {
        $copy = clone $this;
        $copy->items[] = $obligation;

        return $copy;
    }

    /** @return Traversable<int,Obligation> */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function add(Obligation $obligation): void
    {
        $this->items[] = $obligation;
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

    /** @return list<array<string,mixed>> */
    public function toArray(): array
    {
        return array_map(
            static fn (Obligation $obligation): array => [
                'type' => $obligation->type,
                'params' => $obligation->params,
            ],
            $this->items,
        );
    }
}
