<?php
declare(strict_types=1);

namespace App\Infrastructure\Http;

final class Client
{
    public function __construct(
        private readonly string $endpoint,
        private readonly ?string $hmacKey = null,
        private readonly int $timeoutMs = 800
    ) {
    }

    /** @return array{allowed:bool, meta?:array} */
    public function check(string $subject, string $relation, string $resource, array $context = []): array
    {
        $payload = json_encode([
            'subject' => $subject,
            'relation' => $relation,
            'resource' => $resource,
            'context' => $context,
        ], JSON_UNESCAPED_SLASHES);

        $handle = curl_init(rtrim($this->endpoint, '/') . '/check');
        curl_setopt_array($handle, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT_MS => $this->timeoutMs,
        ]);

        $output = curl_exec($handle);
        if ($output === false) {
            return ['allowed' => false, 'meta' => ['error' => curl_error($handle)]];
        }

        $code = curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
        curl_close($handle);
        $decoded = json_decode((string) $output, true);

        return is_array($decoded)
            ? ['allowed' => (bool) ($decoded['allowed'] ?? false), 'meta' => $decoded]
            : ['allowed' => $code === 200];
    }
}
