<?php

declare(strict_types=1);

namespace App\Security\Http;

final class Response
{
    /** @param array<string,string> $headers */
    public function __construct(
        public int $status,
        public array $headers = [],
        public string $body = '',
    ) {
    }
}
