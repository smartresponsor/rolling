<?php
declare(strict_types=1);

namespace App\Net\Http;

use RuntimeException;
use Throwable;

/**
 *
 */

/**
 *
 */
final class RemoteHttpException extends RuntimeException
{
    /**
     * @param int $status
     * @param string $message
     * @param \Throwable|null $prev
     */
    public function __construct(private readonly int $status, string $message = 'remote http error', ?Throwable $prev = null)
    {
        parent::__construct($message, $status, $prev);
    }

    /**
     * @return int
     */
    public function status(): int
    {
        return $this->status;
    }
}
