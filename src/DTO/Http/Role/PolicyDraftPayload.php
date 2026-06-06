<?php

declare(strict_types=1);

namespace App\Rolling\DTO\Http\Role;

final readonly class PolicyDraftPayload
{
    public function __construct(
        public string $tenant,
        public string $expression,
    ) {
    }

    /**
     * @param array<string,mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            tenant: (string) ($payload['tenant'] ?? 't1'),
            expression: (string) ($payload['expr'] ?? ''),
        );
    }
}
