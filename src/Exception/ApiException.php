<?php

declare(strict_types=1);

namespace App\Rolling\Exception;

class ApiException extends \RuntimeException
{
    public function __construct(string $message, public int $status, public array $headers = [], ?\Throwable $prev = null)
    {
        parent::__construct($message, $status, $prev);
    }
}
