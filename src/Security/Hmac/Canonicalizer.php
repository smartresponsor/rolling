<?php

declare(strict_types=1);

<<<<<<< HEAD:src/Security/Hmac/Canonicalizer.php
namespace App\Security\Hmac;
=======
namespace src\Security\Role\Hmac;

>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Security/Role/Hmac/Canonicalizer.php
/**
 *
 */

/**
 *
 */
final class Canonicalizer
{
    /**
     * @param string $m
     * @param string $p
     * @param string $b
     * @param int $ts
     * @param string|null $n
     * @return string
     */
    public static function canonical(string $m, string $p, string $b, int $ts, ?string $n = null): string
    {
        $mh = strtoupper($m);
        $bh = hash('sha256', $b);
        $n = $n ?? '';
        return "v1\n{$mh}\n{$p}\n{$bh}\n{$ts}\n{$n}\n";
    }
}
