<?php
declare(strict_types=1);

namespace Http\Role\V2;

use App\Consistency\Role\TokenSet;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 */

/**
 *
 */
final class ConsistencyHeaders
{
    /**
     * @param \Symfony\Component\HttpFoundation\Response $r
     * @param \App\Consistency\Role\TokenSet $t
     * @return void
     */
    public static function apply(Response $r, TokenSet $t): void
    {
        $etag = '"' . substr($t->hash(), 0, 16) . '"';
        $r->headers->set('ETag', $etag);
        $r->headers->set('X-Role-Consistency', (string)$t);
        // Note: 304 for POST is unusual; we just expose ETag for client-side optimization.
    }
}
