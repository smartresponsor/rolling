<?php

declare(strict_types=1);

namespace App\Rolling\DTO\Http\Role\Pipeline;

final readonly class PipelineEvalPayload
{
    /**
     * @param array<string,mixed> $resource
     * @param array<string,mixed> $attributes
     */
    public function __construct(
        public string $tenant,
        public string $subject,
        public string $action,
        public array $resource,
        public array $attributes,
    ) {
    }

    /**
     * @param array<string,mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            tenant: (string) ($payload['tenant'] ?? 't1'),
            subject: (string) ($payload['subject'] ?? 'u1'),
            action: (string) ($payload['action'] ?? 'read'),
            resource: is_array($payload['resource'] ?? null) ? $payload['resource'] : [],
            attributes: is_array($payload['attrs'] ?? null) ? $payload['attrs'] : [],
        );
    }
}
