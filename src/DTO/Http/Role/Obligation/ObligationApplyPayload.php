<?php

declare(strict_types=1);

namespace App\Rolling\DTO\Http\Role\Obligation;

final readonly class ObligationApplyPayload
{
    /**
     * @param array<string,mixed>      $decision
     * @param array<string,mixed>      $attributes
     * @param array<string,mixed>|null $resource
     */
    public function __construct(
        public string $tenant,
        public string $relation,
        public array $decision,
        public array $attributes,
        public ?array $resource,
        public string $version,
    ) {
    }

    /**
     * @param array<string,mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            tenant: (string) ($payload['tenant'] ?? 't1'),
            relation: (string) ($payload['relation'] ?? 'viewer'),
            decision: is_array($payload['decision'] ?? null) ? $payload['decision'] : ['allowed' => false],
            attributes: is_array($payload['attrs'] ?? null) ? $payload['attrs'] : [],
            resource: is_array($payload['resource'] ?? null) ? $payload['resource'] : null,
            version: (string) ($payload['version'] ?? 'active'),
        );
    }
}
