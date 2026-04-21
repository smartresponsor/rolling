<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\Infrastructure\Rebac;

class HttpClient
{
    /**
     * @param string      $baseUrl
     * @param string|null $token
     */
    public function __construct(private readonly string $baseUrl, private readonly ?string $token = null)
    {
    }

    /**
     * @param string $path
     * @param array  $payload
     *
     * @return array
     */
    public function postJson(string $path, array $payload): array
    {
        $url = rtrim($this->baseUrl, '/').'/'.ltrim($path, '/');
        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n".($this->token ? "Authorization: Bearer {$this->token}\r\n" : ''),
                'content' => json_encode($payload, JSON_UNESCAPED_SLASHES),
                'timeout' => 10,
            ],
        ];
        $res = @file_get_contents($url, false, stream_context_create($opts));
        if (false === $res) {
            return ['ok' => false, 'error' => 'http_failed'];
        }
        $data = json_decode($res, true);

        return ['ok' => true, 'data' => $data];
    }
}
