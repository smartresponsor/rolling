<?php

declare(strict_types=1);

namespace App\ServiceInterface\Tenant;

interface TenantKeyProviderInterface
{
    public function getKey(string $tenant): ?string;

    public function rotate(string $tenant): string;
}
