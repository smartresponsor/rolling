<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\ServiceInterface\Role\Tenant;

/**
 *
 */

/**
 *
 */
interface TenantContextResolverInterface
{
    /**
     * Resolve tenant id from subject/resource/context. Return null if not resolvable.
     * Expected precedence: context.tenant → resource.tenant → subject.tenant
     */
    public function resolve(array $subject, array $resource, array $context = []): ?string;
}
