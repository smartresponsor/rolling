<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Domain\Role\Port;

/**
 *
 */

/**
 *
 */
interface OverridePolicyPort
{
    /** Return true if actor can force-override decision for relation/resource. */
    public function canOverride(string $tenant, string $actor, string $relation, string $resource): bool;
}
