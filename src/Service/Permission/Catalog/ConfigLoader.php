<?php

declare(strict_types=1);

namespace App\Rolling\Service\Permission\Catalog;

use App\Rolling\Service\Permission\Model\PermissionDef;

final class ConfigLoader
{
    /** @return list<PermissionDef> */
    public function loadJsonFile(string $path): array
    {
        $raw = file_get_contents($path);
        if (false === $raw) {
            throw new \RuntimeException("perm_config_read_failed:$path");
        }
        $cfg = json_decode($raw, true);
        if (!is_array($cfg) || !isset($cfg['permissions']) || !is_array($cfg['permissions'])) {
            throw new \RuntimeException('perm_config_bad_json');
        }
        $out = [];
        foreach ($cfg['permissions'] as $row) {
            $out[] = new PermissionDef((string) $row['key'], array_values($row['scopes'] ?? ['global']), (string) ($row['description'] ?? ''), isset($row['component']) ? (string) $row['component'] : null);
        }

        return $out;
    }
}
