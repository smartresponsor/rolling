<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Infra\Role\Residency;

use App\Domain\Role\Port\ResidencyPolicyPort;

/**
 *
 */

/**
 *
 */
final class ResidencyFsPolicy implements ResidencyPolicyPort
{
    /**
     * @param string $file
     */
    public function __construct(private readonly string $file) {} // config/role/residency.json

    /**
     * @param string $tenant
     * @return array
     */
    public function load(string $tenant): array
    {
        if (!is_file($this->file)) {
            return ['allowedRegions' => [], 'defaultRegion' => 'us'];
        }
        $j = json_decode((string) file_get_contents($this->file), true);
        $cfg = is_array($j) ? $j : [];
        return (array) ($cfg[$tenant] ?? ['allowedRegions' => [], 'defaultRegion' => 'us']);
    }
}
