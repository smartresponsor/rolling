<?php

declare(strict_types=1);

namespace App\Rolling\Exception;

final class RateLimitException extends ApiException
{
    /**
     * @return int|null
     */
    public function retryAfterSeconds(): ?int
    {
        $v = $this->headers['retry-after'] ?? $this->headers['Retry-After'] ?? null;
        if (null === $v) {
            return null;
        }
        if (ctype_digit((string) $v)) {
            return (int) $v;
        }

        return null;
    }
}
