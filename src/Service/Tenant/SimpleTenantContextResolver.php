<?php

declare(strict_types=1);

namespace App\Service\Tenant;

use App\ServiceInterface\Tenant\TenantContextResolverInterface;

final class SimpleTenantContextResolver implements TenantContextResolverInterface
{
    public function resolve(array $subject, array $resource, array $context = []): ?string
    {
        if (isset($context['tenant']) && is_string($context['tenant'])) {
            return $context['tenant'];
        }

        if (isset($resource['tenant']) && is_string($resource['tenant'])) {
            return $resource['tenant'];
        }

        if (isset($subject['tenant']) && is_string($subject['tenant'])) {
            return $subject['tenant'];
        }

        return null;
    }
}
