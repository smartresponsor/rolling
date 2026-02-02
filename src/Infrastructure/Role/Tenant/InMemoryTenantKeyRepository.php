<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Infra\Role\Tenant;

use App\InfraInterface\Role\Tenant\TenantKeyRepositoryInterface;

/**
 *
 */

/**
 *
 */
final class InMemoryTenantKeyRepository implements TenantKeyRepositoryInterface
{
    /** @var array */
    private array $map = [];

    /**
     * @param array $seed
     */
    public function __construct(array $seed = [])
    {
        foreach ($seed as $tenant => $key) {
            $this->map[(string)$tenant] = (string)$key;
        }
    }

    /**
     * @param string $tenant
     * @return string|null
     */
    public function get(string $tenant): ?string
    {
        return $this->map[$tenant] ?? null;
    }

    /**
     * @param string $tenant
     * @param string $key
     * @return bool
     */
    public function put(string $tenant, string $key): bool
    {
        $this->map[$tenant] = $key;
        return true;
    }

    /**
     * @return array|string[]
     */
    public function listTenants(): array
    {
        return array_keys($this->map);
    }
}
