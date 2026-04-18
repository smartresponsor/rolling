<?php

declare(strict_types=1);

namespace App\Legacy\Http;

use Psr\Http\Message\ResponseInterface;
use App\Exception\{ApiException,
    BadRequestException,
    ForbiddenException,
    RateLimitException,
    RemoteErrorException,
    UnauthorizedException
};

/**
 *
 */

/**
 *
 */
final class ResponseErrorMapper
{
    /** @throws ApiException */
    public static function throwOnError(ResponseInterface $r): void
    {
        $s = $r->getStatusCode();
        if ($s < 400) {
            return;
        }

        $headers = [];
        foreach ($r->getHeaders() as $k => $v) {
            $headers[$k] = implode(',', $v);
        }
        $body = (string) $r->getBody();
        $msg = "HTTP $s: " . (mb_substr($body, 0, 2000));

        if ($s === 400) {
            throw new BadRequestException($msg, $s, $headers);
        }
        if ($s === 401) {
            throw new UnauthorizedException($msg, $s, $headers);
        }
        if ($s === 403) {
            throw new ForbiddenException($msg, $s, $headers);
        }
        if ($s === 429) {
            throw new RateLimitException($msg, $s, $headers);
        }
        if ($s >= 500) {
            throw new RemoteErrorException($msg, $s, $headers);
        }

        throw new ApiException($msg, $s, $headers);
    }
}
