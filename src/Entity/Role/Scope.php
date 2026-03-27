<?php

declare(strict_types=1);

namespace src\Entity\Role;

/**
 *
 */

/**
 *
 */
final class Scope
{
    /**
     * @param string $k
     */
    private function __construct(private readonly string $k) {}

    public static function global(): self
    {
        return new self('global');
    }

    /**
     * @param string $tenantId
     * @return self
     */
    public static function tenant(string $tenantId): self
    {
        return new self('tenant:' . $tenantId);
    }

    /**
     * @param string $tenantId
     * @param string $resId
     * @return self
     */
    public static function resource(string $tenantId, string $resId): self
    {
        return new self('resource:' . $tenantId . ':' . $resId);
    }

    /**
     * @return string
     */
    public function key(): string
    {
        return $this->k;
    }

    public function type(): string
    {
        return explode(':', $this->k, 2)[0];
    }

    public function tenantId(): ?string
    {
        $parts = explode(':', $this->k);

        return $parts[0] === 'tenant' || $parts[0] === 'resource' ? ($parts[1] ?? null) : null;
    }

    public function resourceId(): ?string
    {
        $parts = explode(':', $this->k);

        return $parts[0] === 'resource' ? ($parts[2] ?? null) : null;
    }
}
