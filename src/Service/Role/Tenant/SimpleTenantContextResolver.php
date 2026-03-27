<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace Tenant;

use App\ServiceInterface\Role\Tenant\TenantContextResolverInterface;

/**
 *
 */

/**
 *
 */
final class SimpleTenantContextResolver implements TenantContextResolverInterface
{
    /**
     * @param array $subject
     * @param array $resource
     * @param array $context
     * @return string|null
     */
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
