<?php

declare(strict_types=1);

namespace App\Security\Admin;

use Symfony\Component\HttpFoundation\Request;

final class Voter
{
    public function __construct(private readonly string $secretPath = __DIR__ . '/../../../var/admin_secret.txt') {}

    /**
     * @param list<string> $needed
     */
    public function isAdmin(Request $req, array $needed = [Roles::OWNER, Roles::OPERATOR]): bool
    {
        $role = (string) ($req->headers->get('X-Role-Admin') ?? '');
        $secret = (string) ($req->headers->get('X-Role-Admin-Secret') ?? '');

        if (!in_array($role, $needed, true)) {
            return false;
        }

        $fileSecret = trim((string) (@file_get_contents($this->secretPath) ?: ''));

        return $fileSecret !== '' && hash_equals($fileSecret, $secret);
    }
}
