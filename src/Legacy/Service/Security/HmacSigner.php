<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Legacy\Service\Security;

use App\Legacy\ServiceInterface\Security\SignerInterface;

/**
 *
 */

/**
 *
 */
final class HmacSigner implements SignerInterface
{
    /**
     * @param string $algo
     */
    public function __construct(private readonly string $algo = 'sha256') {}

    /**
     * @param string $payload
     * @param string $key
     * @return string
     */
    public function sign(string $payload, string $key): string
    {
        $mac = hash_hmac($this->algo, $payload, self::keyDecode($key), true);
        return Base64Url::encode($mac);
    }

    /**
     * @param string $payload
     * @param string $signature
     * @param string $key
     * @return bool
     */
    public function verify(string $payload, string $signature, string $key): bool
    {
        $mac = hash_hmac($this->algo, $payload, self::keyDecode($key), true);
        return hash_equals($mac, Base64Url::decode($signature));
    }

    /**
     * @param string $k
     * @return string
     */
    private static function keyDecode(string $k): string
    {
        // Treat key as base64url; if not b64url, use raw bytes
        $decoded = Base64Url::decode($k);
        return $decoded != false ? $decoded : $k;
    }
}
