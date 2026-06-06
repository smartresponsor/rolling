<?php

declare(strict_types=1);

namespace App\Rolling\DTO\Http\Role;

final readonly class RebacCheckPayload
{
    public function __construct(
        public string $namespace,
        public string $subject,
        public string $object,
        public string $relation,
    ) {
    }

    /**
     * @param array<string,mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            namespace: (string) ($payload['ns'] ?? 'default'),
            subject: (string) ($payload['subject'] ?? ''),
            object: (string) ($payload['object'] ?? ''),
            relation: (string) ($payload['relation'] ?? ''),
        );
    }
}
