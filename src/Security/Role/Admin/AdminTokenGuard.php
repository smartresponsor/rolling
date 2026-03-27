<?php

declare(strict_types=1);

namespace src\Security\Role\Admin;

use RuntimeException;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 */

/**
 *
 */
final class AdminTokenGuard
{
    /**
     * @param string $tokenEnvVar
     */
    public function __construct(private readonly string $tokenEnvVar = 'ROLE_ADMIN_TOKEN') {}

    /**
     * @param \Symfony\Component\HttpFoundation\Request $r
     * @return void
     */
    public function assert(Request $r): void
    {
        $expected = getenv($this->tokenEnvVar) ?: null;
        $got = $r->headers->get('X-Admin-Token');
        if (!$expected || $got !== $expected) {
            throw new RuntimeException('admin_unauthorized');
        }
    }
}
