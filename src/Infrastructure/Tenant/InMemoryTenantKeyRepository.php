<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\Infrastructure\Tenant;

use App\Rolling\InfrastructureInterface\Tenant\TenantKeyRepositoryInterface;

final class InMemoryTenantKeyRepository implements TenantKeyRepositoryInterface
{
    private array $map = [];

    public function __construct(array $seed = [])
    {
        foreach ($seed as $tenant => $key) {
            $this->map[(string) $tenant] = (string) $key;
        }
    }

    public function get(string $tenant): ?string
    {
        return $this->map[$tenant] ?? null;
    }

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
