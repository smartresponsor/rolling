<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Legacy\ServiceInterface\Key;

/**
 * Provide HMAC/enc keys by tenant and key id (kid).
 */
interface KeyProviderInterface
{
    /** @return array{kid:string, material:string} */
    public function getActive(string $tenant): array;

    /** @return array{kid:string, material:string}|null */
    public function getById(string $tenant, string $kid): ?array;

    /** Rotate to new key and return it. @return array{kid:string, material:string} */
    public function rotate(string $tenant): array;
}
