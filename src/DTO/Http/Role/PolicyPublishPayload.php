<?php

declare(strict_types=1);

namespace App\Rolling\DTO\Http\Role;

final readonly class PolicyPublishPayload
{
    public function __construct(
        public string $tenant,
        public string $note,
    ) {
    }

    /**
     * @param array<string,mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            tenant: (string) ($payload['tenant'] ?? 't1'),
            note: (string) ($payload['note'] ?? ''),
        );
    }
}
