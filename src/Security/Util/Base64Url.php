<?php

declare(strict_types=1);

<<<<<<< HEAD:src/Security/Util/Base64Url.php
namespace App\Security\Util;
=======
namespace src\Security\Role\Util;

>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Security/Role/Util/Base64Url.php
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
