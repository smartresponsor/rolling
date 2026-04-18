<?php

declare(strict_types=1);

namespace App\Service\Permission\Catalog;

use App\Service\Permission\Model\PermissionDef;

/**
 *
 */

/**
 *
 */
final class Catalog
{
    /** @var array */
    private array $items = [];

    /**
     * @param \App\Legacy\Permission\Model\PermissionDef $p
     * @return void
     */
    public function add(PermissionDef $p): void
    {
        $this->items[$p->key] = $p;
    }

    /** @return list<PermissionDef> */
    public function all(): array
    {
        return array_values($this->items);
    }

    /** @return list<PermissionDef> */
    public function byComponent(?string $c): array
    {
        if ($c === null) {
            return $this->all();
        }
        return array_values(array_filter($this->items, fn($p) => $p->component === $c));
    }
}
