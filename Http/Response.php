<?php

declare(strict_types=1);

namespace Http;

/**
 *
 */

/**
 *
 */
final class Response
{
    /**
     * @param int $status
     * @param array $headers
     * @param string $body
     */
    public function __construct(public int $status, public array $headers = [], public string $body = '') {}
}
