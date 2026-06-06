<?php

declare(strict_types=1);

namespace App\Rolling\DTO\Http\Role;

final readonly class SecuritySignPayload
{
    /**
     * @param array<string,mixed> $claims
     */
    public function __construct(
        public string $tenant,
        public array $claims,
    ) {
    }

    /**
     * @param array<string,mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            tenant: (string) ($payload['tenant'] ?? 't1'),
            claims: is_array($payload['claims'] ?? null) ? $payload['claims'] : [],
        );
    }
}
