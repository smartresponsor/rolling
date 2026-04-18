<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\InfrastructureInterface\Residency;

/**
 *
 */

/**
 *
 */
interface ResidencyPolicyInterface
{
    /** @return array<string,mixed> tenant config: ['allowedRegions'=>string[], 'defaultRegion'=>string] */
    public function load(string $tenant): array;
}
