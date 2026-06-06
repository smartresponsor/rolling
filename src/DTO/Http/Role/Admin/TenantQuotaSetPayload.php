<?php

declare(strict_types=1);

namespace App\Rolling\DTO\Http\Role\Admin;

final readonly class TenantQuotaSetPayload
{
    public function __construct(public string $tenant, public int $perMinute)
    {
    }

    /**
     * @param array<string,mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            tenant: (string) ($payload['tenant'] ?? ''),
            perMinute: (int) ($payload['per_min'] ?? 0),
        );
    }
}
