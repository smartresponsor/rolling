<?php
declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Legacy\ServiceInterface;

/**
 *
 */

/**
 *
 */
interface ObligationStoreInterface
{
    /** @return array<string,mixed> */
    public function load(string $tenant, string $version = 'active'): array;
}
