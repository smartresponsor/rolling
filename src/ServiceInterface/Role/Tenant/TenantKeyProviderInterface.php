<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Role\Tenant;

interface TenantKeyProviderInterface
{
    public function getKey(string $tenant): ?string;

    /**
     * Rotate key and return new value.
     */
    public function rotate(string $tenant): string;
}
