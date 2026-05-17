<?php

declare(strict_types=1);

namespace App\Rolling\Service\Permission\Catalog;

use App\Rolling\Service\Permission\Model\PermissionDefinitionDto;

final class PermissionCatalog
{
    /** @var array<string, PermissionDefinitionDto> */
    private array $items = [];

    public function add(PermissionDefinitionDto $permission): void
    {
        $this->items[$permission->key] = $permission;
    }

    /** @return list<PermissionDefinitionDto> */
    public function all(): array
    {
        return array_values($this->items);
    }

    /** @return list<PermissionDefinitionDto> */
    public function byComponent(?string $component): array
    {
        if (null === $component) {
            return $this->all();
        }

        return array_values(array_filter(
            $this->items,
            static fn (PermissionDefinitionDto $permission): bool => $permission->component === $component,
        ));
    }
}
