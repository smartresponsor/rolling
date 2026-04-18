<?php

declare(strict_types=1);

namespace App\Entity\Role;

final class Scope
{
    private function __construct(private readonly string $k)
    {
    }

    public static function global(): self
    {
        return new self('global');
    }

    public static function tenant(string $tenantId): self
    {
        return new self('tenant:' . $tenantId);
    }

    public static function resource(string $tenantId, string $resId): self
    {
        return new self('resource:' . $tenantId . ':' . $resId);
    }

    public function key(): string
    {
        return $this->k;
    }

    public function type(): string
    {
        return str_contains($this->k, ':') ? explode(':', $this->k, 2)[0] : $this->k;
    }

    public function tenantId(): ?string
    {
        $parts = explode(':', $this->k);

        return match ($parts[0] ?? $this->k) {
            'tenant' => $parts[1] ?? null,
            'resource' => $parts[1] ?? null,
            default => null,
        };
    }

    public function resourceId(): ?string
    {
        $parts = explode(':', $this->k);

        return (($parts[0] ?? '') === 'resource') ? ($parts[2] ?? null) : null;
    }

    public function __toString(): string
    {
        return $this->k;
    }
}
