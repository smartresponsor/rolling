<?php

declare(strict_types=1);

namespace src\Security\Role\Util;

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
    public static function enc(string $bin): string
    {
        return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
    }

    /**
     * @param string $txt
     * @return string
     */
    public static function dec(string $txt): string
    {
        $re = strtr($txt, '-_', '+/');
        $pad = strlen($re) % 4;
        if ($pad) {
            $re .= str_repeat('=', 4 - $pad);
        }
        return base64_decode($re, true) ?: '';
    }
}
