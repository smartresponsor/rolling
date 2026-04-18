<?php

declare(strict_types=1);

namespace App\Legacy\Http\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 */

/**
 *
 */
final class Consistency
{
    public const STRONG = 'strong';
    public const EVENTUAL = 'eventual';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @return string
     */
    public static function mode(Request $req): string
    {
        $m = $req->query->get('consistency') ?? $req->headers->get('X-Role-Consistency');
        $m = is_string($m) ? strtolower($m) : null;
        return in_array($m, [self::STRONG, self::EVENTUAL], true) ? $m : self::EVENTUAL;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $res
     * @param string $mode
     * @param string|null $token
     * @return void
     */
    public static function applyHeaders(Response $res, string $mode, string $token = null): void
    {
        $res->headers->set('X-Role-Consistency', $mode);
        if ($token !== null) {
            $res->headers->set('X-Role-Consistency-Token', $token);
        }
    }
}
