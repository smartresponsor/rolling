<?php
declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Domain\Role\Port;

/**
 *
 */

/**
 *
 */
interface ResidencyPolicyPort
{
    /** @return array<string,mixed> tenant config: ['allowedRegions'=>string[], 'defaultRegion'=>string] */
    public function load(string $tenant): array;
}
