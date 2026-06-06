<?php

declare(strict_types=1);

namespace App\Rolling\DTO\Http\Role;

final readonly class ResidencyEnforcePayload
{
    /**
     * @param array<string,mixed> $attributes
     */
    public function __construct(
        public string $tenant,
        public array $attributes,
        public string $action,
    ) {
    }

    /**
     * @param array<string,mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            tenant: (string) ($payload['tenant'] ?? 't1'),
            attributes: is_array($payload['attrs'] ?? null) ? $payload['attrs'] : [],
            action: (string) ($payload['action'] ?? 'read'),
        );
    }
}
