<?php
declare(strict_types=1);

namespace App\Net\Http;
/**
 *
 */

/**
 *
 */
interface SimpleHttpClientInterface
{
    /** @return array{status:int, headers:array<string,string>, body:?string} */
    public function request(string $method, string $url, array $headers = [], ?string $body = null, int $timeoutMs = 5000): array;
}
