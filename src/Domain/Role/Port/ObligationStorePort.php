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
interface ObligationStorePort
{
    /** @return array<string,mixed> */
    public function load(string $tenant, string $version = 'active'): array;
}
