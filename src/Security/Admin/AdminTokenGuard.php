<?php

declare(strict_types=1);

namespace App\Security\Admin;

use RuntimeException;
use Symfony\Component\HttpFoundation\Request;

final class AdminTokenGuard
{
<<<<<<< HEAD:src/Security/Admin/AdminTokenGuard.php
    public function __construct(private readonly string $tokenEnvVar = 'ROLE_ADMIN_TOKEN')
    {
    }
=======
    /**
     * @param string $tokenEnvVar
     */
    public function __construct(private readonly string $tokenEnvVar = 'ROLE_ADMIN_TOKEN') {}
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Security/Role/Admin/AdminTokenGuard.php

    public function assert(Request $request): void
    {
        $expected = getenv($this->tokenEnvVar) ?: null;
        $received = $request->headers->get('X-Admin-Token');

        if (!$expected || $received !== $expected) {
            throw new RuntimeException('admin_unauthorized');
        }
    }
}
