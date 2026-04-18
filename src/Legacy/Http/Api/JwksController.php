<?php

declare(strict_types=1);

namespace App\Legacy\Http\Api;

use App\Legacy\Security\Keys\KeyStore;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 *
 */

/**
 *
 */
final class JwksController
{
    private KeyStore $keys;

    /**
     * @param string $keyDir
     */
    public function __construct(string $keyDir = __DIR__ . '/../../../../var/keys')
    {
        $this->keys = new KeyStore($keyDir);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function jwks(): JsonResponse
    {
        return new JsonResponse($this->keys->jwks());
    }
}
