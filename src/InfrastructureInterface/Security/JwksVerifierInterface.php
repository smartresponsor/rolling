<?php

declare(strict_types=1);

namespace App\InfrastructureInterface\Security;

interface JwksVerifierInterface
{
    /** @return array{ok:bool,header:array<string,mixed>,payload:array<string,mixed>,kid:?string} */
    public function verify(string $tenant, string $jwt): array;

    /** @param array<string,mixed> $payload */
    public function signHs256(string $tenant, array $payload, ?string $kid = null): string;
}
