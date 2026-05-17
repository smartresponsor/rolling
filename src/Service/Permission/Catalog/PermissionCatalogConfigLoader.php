<?php

declare(strict_types=1);

namespace App\Rolling\Service\Permission\Catalog;

use App\Rolling\Service\Permission\Model\PermissionDefinitionDto;

final class PermissionCatalogConfigLoader
{
    /** @return list<PermissionDefinitionDto> */
    public function loadJsonFile(string $path): array
    {
        $raw = file_get_contents($path);
        if (false === $raw) {
            throw new \RuntimeException("perm_config_read_failed:$path");
        }

        $config = json_decode($raw, true);
        if (!is_array($config) || !isset($config['permissions']) || !is_array($config['permissions'])) {
            throw new \RuntimeException('perm_config_bad_json');
        }

        $permissions = [];
        foreach ($config['permissions'] as $row) {
            if (!is_array($row)) {
                throw new \RuntimeException('perm_config_bad_permission_row');
            }

            $permissions[] = new PermissionDefinitionDto(
                (string) $row['key'],
                array_values($row['scopes'] ?? ['global']),
                (string) ($row['description'] ?? ''),
                isset($row['component']) ? (string) $row['component'] : null,
            );
        }

        return $permissions;
    }
}
