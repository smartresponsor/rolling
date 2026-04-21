<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Rebac;

/**
 * Constraints for namespace hops and tenant boundaries.
 */
interface NamespaceConstraintInterface
{
    /**
     * @param string $fromNamespace
     * @param string $toNamespace
     *
     * @return bool
     */
    public function canTraverse(string $fromNamespace, string $toNamespace): bool;

    /** Enforce tenant boundary: must be same tenant unless explicitly allowed. */
    public function isTenantAllowed(string $fromTenant, string $toTenant): bool;
}
