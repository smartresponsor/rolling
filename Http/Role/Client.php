<?php

declare(strict_types=1);

namespace Http\Role;

/**
 *
 */

/**
 *
 */
final class Client
{
    /**
     * @param string $endpoint
     * @param string|null $hmacKey
     * @param int $timeoutMs
     */
    public function __construct(
        private readonly string  $endpoint,
        private readonly ?string $hmacKey = null,
        private readonly int     $timeoutMs = 800,
    ) {}

    /** @return array{allowed:bool, meta?:array} */
    public function check(string $subject, string $relation, string $resource, array $context = []): array
    {
        // Minimal HTTP client via curl; replace by Symfony HttpClient in real app
        $payload = json_encode(['subject' => $subject, 'relation' => $relation, 'resource' => $resource, 'context' => $context], JSON_UNESCAPED_SLASHES);
        $ch = curl_init(rtrim($this->endpoint, '/') . '/check');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT_MS => $this->timeoutMs,
        ]);
        $out = curl_exec($ch);
        if ($out === false) {
            return ['allowed' => false, 'meta' => ['error' => curl_error($ch)]];
        }
        $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        $j = json_decode((string) $out, true);
        return is_array($j) ? ['allowed' => (bool) ($j['allowed'] ?? false), 'meta' => $j] : ['allowed' => $code == 200];
    }
}
