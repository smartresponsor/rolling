<?php

declare(strict_types=1);

namespace App\Rolling\DTO\Http\Role;

final readonly class RoleCheckPayload
{
    /**
     * @param array<string,mixed>     $context
     * @param array<int|string,mixed> $obligations
     */
    public function __construct(
        public string $tenant,
        public string $subject,
        public string $relation,
        public string $resource,
        public array $context,
        public array $obligations,
    ) {
    }

    /**
     * @param array<string,mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            tenant: (string) ($payload['tenant'] ?? 't1'),
            subject: (string) ($payload['subject'] ?? ''),
            relation: (string) ($payload['relation'] ?? ''),
            resource: (string) ($payload['resource'] ?? ''),
            context: is_array($payload['context'] ?? null) ? $payload['context'] : [],
            obligations: is_array($payload['obligations'] ?? null) ? $payload['obligations'] : [],
        );
    }
}
