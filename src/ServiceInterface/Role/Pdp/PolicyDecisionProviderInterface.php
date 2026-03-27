<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\ServiceInterface\Role\Pdp;

/**
 *
 */

/**
 *
 */
interface PolicyDecisionProviderInterface
{
    /**
     * @param array $subject {id, roles[], tenant?, ...}
     * @param string $action e.g., "can_read"
     * @param array $resource {type, id, tenant?, ownerId?, ...}
     * @param array $context {tenant?, requestId?, ...}
     */
    public function isAllowed(array $subject, string $action, array $resource, array $context = []): bool;
}
