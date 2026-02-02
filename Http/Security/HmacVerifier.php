<?php
declare(strict_types=1);

namespace Http\Security;

/**
 *
 */

/**
 *
 */
final class HmacVerifier
{
    /**
     * @param string $secret
     * @param int $allowedSkewSec
     */
    public function __construct(private readonly string $secret, private readonly int $allowedSkewSec = 300)
    {
    }

    /** @return array{ok:bool, reason?:string} */
    public function verify(string $method, string $path, string $dateRfc1123, string $body, string $signatureHeader): array
    {
        if ($dateRfc1123 === '') return ['ok' => false, 'reason' => 'missing_date'];
        $ts = strtotime($dateRfc1123);
        if ($ts === false) return ['ok' => false, 'reason' => 'bad_date'];
        $now = time();
        if (abs($now - $ts) > $this->allowedSkewSec) return ['ok' => false, 'reason' => 'date_skew'];

        if ($signatureHeader === '') return ['ok' => false, 'reason' => 'missing_signature'];
        // expected format: v1=base64(hmac_sha256("METHOD path\nDate\nBody"))
        $parts = explode('=', $signatureHeader, 2);
        if (count($parts) !== 2 || $parts[0] !== 'v1') return ['ok' => false, 'reason' => 'bad_signature_format'];
        $given = $parts[1];

        $base = strtoupper($method) . ' ' . $path . "\n" . $dateRfc1123 . "\n" . $body;
        $calc = base64_encode(hash_hmac('sha256', $base, $this->secret, true));

        if (!hash_equals($calc, $given)) return ['ok' => false, 'reason' => 'signature_mismatch'];

        return ['ok' => true];
    }

    /** Детерминированный nonce по умолчанию: hash(Date + \n + Body) */
    public static function derivedNonce(string $dateRfc1123, string $body): string
    {
        return hash('sha256', $dateRfc1123 . "\n" . $body);
    }
}
