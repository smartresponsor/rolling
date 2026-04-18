<?php

declare(strict_types=1);

<<<<<<< HEAD:src/Service/Permission/Catalog/CatalogService.php
namespace App\Service\Permission\Catalog;
=======
namespace App\Permission\Role\Catalog;

>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Permission/Role/Catalog/CatalogService.php
/**
 *
 */

/**
 *
 */
final class CatalogService
{
    /**
     * @param \App\Legacy\Permission\Catalog\Catalog $cat
     * @param \App\Legacy\Permission\Catalog\Hasher $hasher
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
