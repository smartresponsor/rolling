<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Infrastructure\Admin;

use App\ServiceInterface\Admin\OverridePolicyInterface;

/**
 *
 */

/**
 *
 */
final class OverrideFsPolicy implements OverridePolicyInterface
{
    /**
     * @param string $baseDir
     */
    public function __construct(private readonly string $baseDir) {} // var/admin/override.json

    /**
     * @param string $tenant
     * @param string $actor
     * @param string $relation
     * @param string $resource
     * @return bool
     */
    public function canOverride(string $tenant, string $actor, string $relation, string $resource): bool
    {
        $file = $this->baseDir . '/override.json';
        $j = is_file($file) ? json_decode((string) file_get_contents($file), true) : [];
        $arr = is_array($j) ? $j : [];
        $allow = (array) ($arr[$tenant]['allow'] ?? []);
        return in_array($actor, $allow, true);
    }
}
