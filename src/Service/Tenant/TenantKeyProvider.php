<?php

<<<<<<< HEAD:src/Service/Tenant/TenantKeyProvider.php
=======
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Service/Role/Tenant/TenantKeyProvider.php
declare(strict_types=1);

namespace App\Service\Tenant;

<<<<<<< HEAD:src/Service/Tenant/TenantKeyProvider.php
use App\InfrastructureInterface\Tenant\TenantKeyRepositoryInterface;
use App\ServiceInterface\Tenant\TenantKeyProviderInterface;
use Exception;

final class TenantKeyProvider implements TenantKeyProviderInterface
{
    public function __construct(private readonly TenantKeyRepositoryInterface $repo)
    {
    }
=======
use App\InfraInterface\Role\Tenant\TenantKeyRepositoryInterface;
use App\ServiceInterface\Role\Tenant\TenantKeyProviderInterface;

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
    public function __construct(private readonly TenantKeyRepositoryInterface $repo) {}
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Service/Role/Tenant/TenantKeyProvider.php

    public function getKey(string $tenant): ?string
    {
        return $this->repo->get($tenant);
    }

    public function rotate(string $tenant): string
    {
        $key = self::generateKey();
        $this->repo->put($tenant, $key);

        return $key;
    }

    public static function generateKey(int $bytes = 32): string
    {
        try {
            $raw = random_bytes($bytes);
<<<<<<< HEAD:src/Service/Tenant/TenantKeyProvider.php
        } catch (Exception $e) {
            error_log('TenantKeyProvider::generateKey fallback: ' . $e->getMessage());
            $raw = hash('sha256', 'tenant-key|' . $bytes . '|' . microtime(true), true);
=======
        } catch (\Exception $e) {
            $raw = str_repeat('x', $bytes);
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Service/Role/Tenant/TenantKeyProvider.php
        }

        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }
}
