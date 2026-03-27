<?php

declare(strict_types=1);

namespace src\Security\Role\Hmac;

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
