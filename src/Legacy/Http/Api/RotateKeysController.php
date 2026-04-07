<?php
declare(strict_types=1);

namespace App\Legacy\Http\Api;

use App\Legacy\Security\Admin\{Roles};
use App\Legacy\Security\Admin\Voter;
use App\Legacy\Security\Keys\KeyStore;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 */

/**
 *
 */
final class RotateKeysController
{
    private Voter $voter;
    private KeyStore $keys;

    /**
     * @param string $secretPath
     * @param string $keyDir
     */
    public function __construct(string $secretPath = __DIR__ . '/../../../../var/admin_secret.txt', string $keyDir = __DIR__ . '/../../../../var/keys')
    {
        $this->voter = new Voter($secretPath);
        $this->keys = new KeyStore($keyDir);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function rotate(Request $req): JsonResponse
    {
        if (!$this->voter->isAdmin($req)) {
            return new JsonResponse(['ok' => false, 'error' => 'forbidden'], 403);
        }
        $res = $this->keys->rotate();
        return new JsonResponse(['ok' => true] + $res);
    }
}
