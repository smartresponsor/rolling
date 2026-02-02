<?php
declare(strict_types=1);

namespace SmartResponsor\RoleSdk\V2\Exception;
/**
 *
 */

/**
 *
 */
final class RateLimitException extends ApiException
{
    /**
     * @return int|null
     */
    public function retryAfterSeconds(): ?int
    {
        $v = $this->headers['retry-after'] ?? $this->headers['Retry-After'] ?? null;
        if ($v === null) return null;
        if (ctype_digit((string)$v)) return (int)$v;
        return null;
    }
}
