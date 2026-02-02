<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace Tenant;

use App\InfraInterface\Role\Tenant\TenantKeyRepositoryInterface;
use App\Service\Role\Tenant\TenantKeyProviderInterface;
use App\ServiceInterface\Role;

/
Tenant / TenantKeyProviderInterface;

/**
 *
 */

/**
 *
 */
final class TenantKeyProvider implements TenantKeyProviderInterface
{
    /**
     * @param \App\InfraInterface\Role\Tenant\TenantKeyRepositoryInterface $repo
     */
    public function __construct(private readonly TenantKeyRepositoryInterface $repo)
    {
    }

    /**
     * @param string $tenant
     * @return string|null
     */
    public function getKey(string $tenant): ?string
    {
        return $this->repo->get($tenant);
    }

    /**
     * @param string $tenant
     * @return string
     */
    public function rotate(string $tenant): string
    {
        $key = self::generateKey();
        $this->repo->put($tenant, $key);
        return $key;
    }

    /**
     * @param int $bytes
     * @return string
     */
    public static function generateKey(int $bytes = 32): string
    {
        try {
            $raw = random_bytes($bytes);
        } catch (\Exception $e) {
        }
        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }
}
