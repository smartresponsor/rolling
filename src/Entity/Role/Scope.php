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
    private function __construct(private readonly string $k)
    {
    }

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
}
