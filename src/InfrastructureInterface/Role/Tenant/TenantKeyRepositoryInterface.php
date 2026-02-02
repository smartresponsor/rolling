<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\InfraInterface\Role\Tenant;

/**
 *
 */

/**
 *
 */
interface TenantKeyRepositoryInterface
{
    /**
     * @return string|null base64url-encoded secret or null if not found
     */
    public function get(string $tenant): ?string;

    /**
     * @param string $tenant
     * @param string $key
     * @return bool
     */
    public function put(string $tenant, string $key): bool;

    /**
     * @return string[] list of tenant ids
     */
    public function listTenants(): array;
}
