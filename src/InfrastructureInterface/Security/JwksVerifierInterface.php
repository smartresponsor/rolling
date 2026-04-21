<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Rolling\InfrastructureInterface\Security;

interface JwksVerifierInterface
{
    /** Verify JWT (HS256 or RS256) using HMAC store or JWKS. Return ['ok'=>bool,'header'=>array,'payload'=>array,'kid'=>?string]. */
    public function verify(string $tenant, string $jwt): array;

    /** Sign payload with HS256 using current key -> compact JWT with kid. */
    public function signHs256(string $tenant, array $payload, ?string $kid = null): string;
}
