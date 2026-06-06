<?php

declare(strict_types=1);

namespace App\Rolling\DTO\Http\Role;

final readonly class AccessCheckPayload
{
    /**
     * @param array<string,mixed> $payload
     */
    public function __construct(public array $payload)
    {
    }

    /**
     * @param array<string,mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self($payload);
    }
}
