<?php

declare(strict_types=1);

namespace App\Rolling\Service\Permission\Catalog;

use App\Rolling\Service\Permission\Model\PermissionDefinitionDto;

final class PermissionCatalogSnapshotService
{
    public function __construct(private readonly PermissionCatalog $catalog, private readonly PermissionCatalogVersionHasher $hasher)
    {
    }

    /** @return array{version:string, items:list<array<string,mixed>>} */
    public function snapshot(?string $component = null): array
    {
        $items = null !== $component ? $this->catalog->byComponent($component) : $this->catalog->all();
        $version = $this->hasher->hash($items);

        return [
            'version' => $version,
            'items' => array_map(static fn (PermissionDefinitionDto $permission): array => $permission->toArray(), $items),
        ];
    }
}
