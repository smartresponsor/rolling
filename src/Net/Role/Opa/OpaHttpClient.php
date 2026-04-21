<?php

declare(strict_types=1);

namespace App\Rolling\Net\Role\Opa;

use App\Rolling\InfrastructureInterface\Net\Opa\OpaClientInterface;

final class OpaHttpClient implements OpaClientInterface
{
    /**
     * @param string $baseUrl
     * @param int    $timeoutMs
     * @param array  $headers
     */
    public function __construct(
        private readonly string $baseUrl, // e.g. http://127.0.0.1:8181
        private readonly int $timeoutMs = 1500,
        private readonly array $headers = [], // e.g. ['Authorization' => 'Bearer ...']
    ) {
    }

    /**
     * @param string $dataPath
     * @param array  $input
     *
     * @return array
     */
    public function evaluate(string $dataPath, array $input): array
    {
        $url = rtrim($this->baseUrl, '/').'/v1/data/'.ltrim($dataPath, '/');
        $payload = json_encode(['input' => $input], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if (false === $payload) {
            throw new \RuntimeException('json encode failed');
        }

        $hdrs = array_merge(['Content-Type' => 'application/json'], $this->headers);
        $hdrLines = [];
        foreach ($hdrs as $k => $v) {
            $hdrLines[] = $k.': '.$v;
        }

        $ctx = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $hdrLines),
                'content' => $payload,
                'timeout' => max(1, (int) ceil($this->timeoutMs / 1000)),
                'ignore_errors' => true,
            ],
        ]);
        $resp = @file_get_contents($url, false, $ctx);
        if (false === $resp) {
            $e = error_get_last();
            throw new \RuntimeException('OPA request failed: '.($e['message'] ?? 'unknown'));
        }

        /** @var array<string,mixed> $out */
        $out = json_decode($resp, true);
        if (!is_array($out)) {
            throw new \RuntimeException('OPA invalid JSON response');
        }

        return $out;
    }
}
