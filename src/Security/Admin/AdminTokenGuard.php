<?php
declare(strict_types=1);

namespace App\Security\Admin;

use RuntimeException;
use Symfony\Component\HttpFoundation\Request;

final class AdminTokenGuard
{
    public function __construct(private readonly string $tokenEnvVar = 'ROLE_ADMIN_TOKEN')
    {
    }

    public function assert(Request $request): void
    {
        $expected = getenv($this->tokenEnvVar) ?: null;
        $received = $request->headers->get('X-Admin-Token');

        if (!$expected || $received !== $expected) {
            throw new RuntimeException('admin_unauthorized');
        }
    }
}
