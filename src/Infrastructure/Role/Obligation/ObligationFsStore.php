<?php
declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Infra\Role\Obligation;

use App\Domain\Role\Port\ObligationStorePort;

/**
 *
 */

/**
 *
 */
final class ObligationFsStore implements ObligationStorePort
{
    /**
     * @param string $baseDir
     */
    public function __construct(private readonly string $baseDir)
    {
    } // var/policy/<tenant>/<version>/obligations.json

    /**
     * @param string $tenant
     * @param string $version
     * @return array[]
     */
    public function load(string $tenant, string $version = 'active'): array
    {
        $p = rtrim($this->baseDir, '/') . "/$tenant/$version/obligations.json";
        if (!is_file($p)) return ['rules' => []];
        $j = json_decode((string)file_get_contents($p), true);
        return is_array($j) ? $j : ['rules' => []];
    }
}
