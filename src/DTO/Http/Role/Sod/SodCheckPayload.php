<?php

declare(strict_types=1);

namespace App\Rolling\DTO\Http\Role\Sod;

final readonly class SodCheckPayload
{
    /**
     * @param array<string,mixed> $attributes
     */
    public function __construct(public array $attributes)
    {
    }

    /**
     * @param array<string,mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(is_array($payload['attrs'] ?? null) ? $payload['attrs'] : []);
    }
}
