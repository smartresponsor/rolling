<?php
declare(strict_types=1);

namespace App\Legacy\Security\Admin;

use Symfony\Component\HttpFoundation\Request;

/**
 *
 */

/**
 *
 */
final class Voter
{
    /**
     * @param string $secretPath
     */
    public function __construct(private readonly string $secretPath = __DIR__ . '/../../../../var/admin_secret.txt')
    {
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @param array $needed
     * @return bool
     */
    public function isAdmin(Request $req, array $needed = [Roles::OWNER, Roles::OPERATOR]): bool
    {
        $role = (string)($req->headers->get('X-Role-Admin') ?? '');
        $sec = (string)($req->headers->get('X-Role-Admin-Secret') ?? '');
        if (!in_array($role, $needed, true)) return false;
        $fileSecret = @file_get_contents($this->secretPath) ?: '';
        $fileSecret = trim($fileSecret);
        return $fileSecret !== '' && hash_equals($fileSecret, $sec);
    }
}
