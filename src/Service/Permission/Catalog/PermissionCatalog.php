<?php

declare(strict_types=1);

namespace App\Rolling\Service\Permission\Catalog;

use App\Rolling\DTO\Permission\PermissionDefinitionDTO;

final class PermissionCatalog
{
    /** @var array<string, PermissionDefinitionDTO> */
    private array $items = [];

    public function add(PermissionDefinitionDTO $permission): void
    {
        $this->items[$permission->key] = $permission;
    }

    /** @return list<PermissionDefinitionDTO> */
    public function all(): array
    {
        return array_values($this->items);
    }

    /** @return list<PermissionDefinitionDTO> */
    public function byComponent(?string $component): array
    {
        if (null === $component) {
            return $this->all();
        }

        return array_values(array_filter(
            $this->items,
            static fn (PermissionDefinitionDTO $permission): bool => $permission->component === $component,
        ));
    }
}
