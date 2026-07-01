<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Rolling\Infrastructure\Security;

final class JoseUtil
{
    public static function b64url(string $s): string
    {
        return rtrim(strtr(base64_encode($s), '+/', '-_'), '=');
    }

    public static function b64urld(string $s): string
    {
        $s = strtr($s, '-_', '+/');
        $pad = strlen($s) % 4;
        if ($pad) {
            $s .= str_repeat('=', 4 - $pad);
        }

        return (string) base64_decode($s);
    }

    public static function hmac256(string $data, string $hexKey): string
    {
        return hash_hmac('sha256', $data, hex2bin($hexKey), true);
    }

    public static function rs256_verify(string $data, string $sigB64, string $pem): bool
    {
        $sig = self::b64urld($sigB64);
        $ok = openssl_verify($data, $sig, $pem, OPENSSL_ALGO_SHA256);

        return 1 === $ok;
    }
}
