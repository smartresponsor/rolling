<?php

declare(strict_types=1);

namespace App\Service\Permission\Catalog;
/**
 *
 */

/**
 *
 */
final class CatalogService
{
    /**
     * @param \App\Service\Permission\Catalog\Catalog $cat
     * @param \App\Service\Permission\Catalog\Hasher $hasher
     */
    public function __construct(private readonly Catalog $cat, private readonly Hasher $hasher) {}

    /** @return array{version:string, items:list<array<string,mixed>>} */
    public function snapshot(?string $component = null): array
    {
        $items = $component ? $this->cat->byComponent($component) : $this->cat->all();
        $ver = $this->hasher->hash($items);
        return ['version' => $ver, 'items' => array_map(fn($p) => $p->toArray(), $items)];
    }
}
