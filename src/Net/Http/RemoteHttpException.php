<?php

declare(strict_types=1);

namespace App\Rolling\Net\Http;

final class RemoteHttpException extends \RuntimeException
{
    public function __construct(private readonly int $status, string $message = 'remote http error', ?\Throwable $prev = null)
    {
        parent::__construct($message, $status, $prev);
    }

    public function status(): int
    {
        return $this->status;
    }
}
