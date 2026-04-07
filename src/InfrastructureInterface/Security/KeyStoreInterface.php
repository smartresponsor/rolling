<?php

declare(strict_types=1);

namespace App\InfrastructureInterface\Security;

interface KeyStoreInterface
{
    /** @return array{kid:string,key:string} */
    public function currentHmac(string $tenant): array;

    public function rotateHmac(string $tenant, ?string $note = null): string;

    public function loadHmac(string $tenant, string $kid): ?string;

    /** @return array<string,mixed> */
    public function jwks(string $tenant): array;

    /** @param array<string,mixed> $jwks */
    public function putJwks(string $tenant, array $jwks): void;
}
