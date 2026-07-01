<?php

declare(strict_types=1);

namespace App\Rolling\Security\Util;

final class Base64Url
{
    public static function enc(string $bin): string
    {
        return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
    }

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
