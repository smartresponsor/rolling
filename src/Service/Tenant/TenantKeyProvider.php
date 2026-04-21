<?php

declare(strict_types=1);

namespace App\Rolling\Service\Tenant;

use App\Rolling\InfrastructureInterface\Tenant\TenantKeyRepositoryInterface;
use App\Rolling\ServiceInterface\Tenant\TenantKeyProviderInterface;

final class TenantKeyProvider implements TenantKeyProviderInterface
{
    public function __construct(private readonly TenantKeyRepositoryInterface $repo)
    {
    }

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
        } catch (\Exception $e) {
            error_log('TenantKeyProvider::generateKey fallback: '.$e->getMessage());
            $raw = hash('sha256', 'tenant-key|'.$bytes.'|'.microtime(true), true);
        }

        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }
}
