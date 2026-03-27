<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\ServiceInterface\Role\Security;

/**
 *
 */

/**
 *
 */
interface SignerInterface
{
    /**
     * @param string $payload
     * @param string $key
     * @return string
     */
    public function sign(string $payload, string $key): string;

    /**
     * @param string $payload
     * @param string $signature
     * @param string $key
     * @return bool
     */
    public function verify(string $payload, string $signature, string $key): bool;
}
