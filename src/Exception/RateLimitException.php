<?php

declare(strict_types=1);

<<<<<<< HEAD
namespace App\Exception;
=======
namespace SmartResponsor\RoleSdk\V2\Exception;

>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa
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
        if ($v === null) {
            return null;
        }
        if (ctype_digit((string) $v)) {
            return (int) $v;
        }
        return null;
    }
}
