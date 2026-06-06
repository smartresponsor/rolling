<?php

declare(strict_types=1);

namespace App\Rolling\DTO\Http\Role\Admin;

final readonly class TenantRestorePayload
{
    public function __construct(public string $path)
    {
    }

    /**
     * @param array<string,mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self((string) ($payload['path'] ?? ''));
    }
}
