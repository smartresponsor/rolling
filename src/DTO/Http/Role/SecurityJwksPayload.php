<?php

declare(strict_types=1);

namespace App\Rolling\DTO\Http\Role;

final readonly class SecurityJwksPayload
{
    /**
     * @param array<string,mixed> $jwks
     */
    public function __construct(
        public string $tenant,
        public array $jwks,
    ) {
    }

    /**
     * @param array<string,mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            tenant: (string) ($payload['tenant'] ?? 't1'),
            jwks: is_array($payload['jwks'] ?? null) ? $payload['jwks'] : ['keys' => []],
        );
    }
}
