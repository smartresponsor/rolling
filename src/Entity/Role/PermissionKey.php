<?php

declare(strict_types=1);

namespace src\Entity\Role;

/**
 *
 */

/**
 *
 */
final class PermissionKey
{
    /**
     * @param string $v
     */
    public function __construct(private readonly string $v) {}

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->v;
    }
}
