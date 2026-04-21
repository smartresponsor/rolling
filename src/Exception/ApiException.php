<?php

declare(strict_types=1);

namespace App\Rolling\Exception;

class ApiException extends \RuntimeException
{
    /**
     * @param string          $message
     * @param int             $status
     * @param array           $headers
     * @param \Throwable|null $prev
     */
    public function __construct(string $message, public int $status, public array $headers = [], ?\Throwable $prev = null)
    {
        parent::__construct($message, $status, $prev);
    }
}
