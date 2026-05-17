<?php

declare(strict_types=1);

namespace App\Rolling\Service\Permission\Catalog;

use App\Rolling\Service\Permission\Model\PermissionDefinitionDto;

final class PermissionCatalogVersionHasher
{
    /** @param list<PermissionDefinitionDto> $items */
    public function hash(array $items): string
    {
        usort($items, static fn (PermissionDefinitionDto $left, PermissionDefinitionDto $right): int => strcmp($left->key, $right->key));
        $payload = array_map(static fn (PermissionDefinitionDto $permission): array => $permission->toArray(), $items);
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);

        return hash('sha256', (string) $json);
    }
}
