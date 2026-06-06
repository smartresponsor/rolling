<?php

declare(strict_types=1);

namespace App\Rolling\DTO\Http\Role\Admin;

final readonly class TenantBackupPayload
{
    public function __construct(public string $tenant)
    {
    }

    /**
     * @param array<string,mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self((string) ($payload['tenant'] ?? ''));
    }
}
