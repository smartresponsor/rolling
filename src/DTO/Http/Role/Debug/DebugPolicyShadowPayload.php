<?php

declare(strict_types=1);

namespace App\Rolling\DTO\Http\Role\Debug;

final readonly class DebugPolicyShadowPayload
{
    /**
     * @param array<string,mixed> $scope
     * @param array<string,mixed> $context
     */
    public function __construct(
        public string $subject,
        public string $action,
        public array $scope,
        public array $context,
    ) {
    }

    /**
     * @param array<string,mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            subject: (string) ($payload['subject'] ?? ''),
            action: (string) ($payload['action'] ?? ''),
            scope: is_array($payload['scope'] ?? null) ? $payload['scope'] : [],
            context: is_array($payload['context'] ?? null) ? $payload['context'] : [],
        );
    }
}
