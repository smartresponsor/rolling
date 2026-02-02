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
interface KeyStorePort
{
    /** Return current HMAC key bytes and kid for tenant. @return array{kid:string,key:string} */
    public function currentHmac(string $tenant): array;

    /** Rotate HMAC key, return new kid. */
    public function rotateHmac(string $tenant, ?string $note = null): string;

    /** Load archived HMAC by kid or null. */
    public function loadHmac(string $tenant, string $kid): ?string;

    /** JWKS get for tenant (public keys). @return array<string,mixed> */
    public function jwks(string $tenant): array;

    /** Replace JWKS for tenant. */
    public function putJwks(string $tenant, array $jwks): void;
}
