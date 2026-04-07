<?php

declare(strict_types=1);

namespace App\ServiceInterface\Tenant;

interface TenantContextResolverInterface
{
    public function resolve(array $subject, array $resource, array $context = []): ?string;
}
