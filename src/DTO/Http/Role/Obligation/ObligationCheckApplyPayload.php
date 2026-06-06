<?php

declare(strict_types=1);

namespace App\Rolling\DTO\Http\Role\Obligation;

final readonly class ObligationCheckApplyPayload
{
    /**
     * @param array<string,mixed> $attributes
     * @param array<string,mixed> $resource
     */
    public function __construct(
        public string $tenant,
        public string $relation,
        public string $subject,
        public array $attributes,
        public array $resource,
        public string $version,
    ) {
    }

    /**
     * @param array<string,mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        $attributes = is_array($payload['attrs'] ?? null) ? $payload['attrs'] : [];

        return new self(
            tenant: (string) ($payload['tenant'] ?? 't1'),
            relation: (string) ($payload['relation'] ?? 'viewer'),
            subject: (string) ($payload['subject'] ?? ($attributes['subject'] ?? 'anonymous')),
            attributes: $attributes,
            resource: is_array($payload['resource'] ?? null) ? $payload['resource'] : [],
            version: (string) ($payload['version'] ?? 'active'),
        );
    }
}
