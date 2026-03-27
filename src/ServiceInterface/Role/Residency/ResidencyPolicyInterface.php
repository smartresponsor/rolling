<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace src\ServiceInterface\Role\Residency;

/**
 * Decide where tenant data must reside.
 */
interface ResidencyPolicyInterface
{
    /**
     * @param string $tenant
     * @return string
     */
    public function regionForTenant(string $tenant): string; // e.g. 'us', 'eu'
}
