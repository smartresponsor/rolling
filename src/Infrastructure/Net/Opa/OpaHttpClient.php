<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Net\Opa;

use App\Rolling\InfrastructureInterface\Net\Opa\OpaClientInterface;

final class OpaHttpClient implements OpaClientInterface
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly int $timeoutMs = 1500,
        private readonly array $headers = [],
    ) {
    }

    public function evaluate(string $dataPath, array $input): array
    {
        $url = rtrim($this->baseUrl, '/').'/v1/data/'.ltrim($dataPath, '/');
        $payload = json_encode(['input' => $input], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if (false === $payload) {
            throw new \RuntimeException('json encode failed');
        }

        $headers = array_merge(['Content-Type' => 'application/json'], $this->headers);
        $lines = [];
        foreach ($headers as $key => $value) {
            $lines[] = $key.': '.$value;
        }

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $lines),
                'content' => $payload,
                'timeout' => max(1, (int) ceil($this->timeoutMs / 1000)),
                'ignore_errors' => true,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);
        if (false === $response) {
            $error = error_get_last();
            throw new \RuntimeException('OPA request failed: '.($error['message'] ?? 'unknown'));
        }

        $decoded = json_decode($response, true);
        if (!is_array($decoded)) {
            throw new \RuntimeException('OPA invalid JSON response');
        }

        return $decoded;
    }
}
