<?php

declare(strict_types=1);

namespace App\Rolling\DTO\Http\Role\Admin;

final readonly class AdminApprovalStartPayload
{
    /**
     * @param array<string,mixed> $options
     */
    public function __construct(
        public string $tenant,
        public string $relation,
        public string $resource,
        public string $requester,
        public array $options,
    ) {
    }

    /**
     * @param array<string,mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            tenant: (string) ($payload['tenant'] ?? 't1'),
            relation: (string) ($payload['relation'] ?? 'change-policy'),
            resource: (string) ($payload['resource'] ?? 'policy:active'),
            requester: (string) ($payload['requester'] ?? 'user:unknown'),
            options: is_array($payload['opts'] ?? null) ? $payload['opts'] : [],
        );
    }
}
