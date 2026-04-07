<?php

declare(strict_types=1);

namespace App\Security\Http;

final class HmacRequestVerifier
{
    public function __construct(
        private readonly string $secret,
        private readonly int $allowedSkewSec = 300,
    ) {
    }

    /** @return array{ok:bool, reason?:string} */
    public function verify(string $method, string $path, string $dateRfc1123, string $body, string $signatureHeader): array
    {
        if ($dateRfc1123 === '') {
            return ['ok' => false, 'reason' => 'missing_date'];
        }

        $ts = strtotime($dateRfc1123);
        if ($ts === false) {
            return ['ok' => false, 'reason' => 'bad_date'];
        }

        if (abs(time() - $ts) > $this->allowedSkewSec) {
            return ['ok' => false, 'reason' => 'date_skew'];
        }

        if ($signatureHeader === '') {
            return ['ok' => false, 'reason' => 'missing_signature'];
        }

        $parts = explode('=', $signatureHeader, 2);
        if (count($parts) !== 2 || $parts[0] !== 'v1') {
            return ['ok' => false, 'reason' => 'bad_signature_format'];
        }

        $base = strtoupper($method) . ' ' . $path . "\n" . $dateRfc1123 . "\n" . $body;
        $expected = base64_encode(hash_hmac('sha256', $base, $this->secret, true));

        if (!hash_equals($expected, $parts[1])) {
            return ['ok' => false, 'reason' => 'signature_mismatch'];
        }

        return ['ok' => true];
    }

    public static function derivedNonce(string $dateRfc1123, string $body): string
    {
        return hash('sha256', $dateRfc1123 . "\n" . $body);
    }
}
