<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Legacy\Service\Security;

/**
 *
 */

/**
 *
 */
final class Base64Url
{
    /**
     * @param string $bin
     * @return string
     */
    public static function encode(string $bin): string
    {
        return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
    }

    /**
     * @param string $b64
     * @return string
     */
    public static function decode(string $b64): string
    {
        $pad = strlen($b64) % 4;
        if ($pad) {
            $b64 .= str_repeat('=', 4 - $pad);
        }
        return base64_decode(strtr($b64, '-_', '+/'));
    }
}
