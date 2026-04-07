<?php declare(strict_types=1);

namespace App\Security\Hmac;

use App\Security\Util\Base64Url;

/**
 *
 */

/**
 *
 */
final class Signer
{
    /**
     * @param string $keyId
     * @param string $secret
     */
    public function __construct(private readonly string $keyId, private readonly string $secret)
    {
    }

    /**
     * @param string $m
     * @param string $p
     * @param string $b
     * @param int|null $ts
     * @param string|null $n
     * @return array
     */
    public function sign(string $m, string $p, string $b, ?int $ts = null, ?string $n = null): array
    {
        $ts = $ts ?? time();
        $canon = Canonicalizer::canonical($m, $p, $b, $ts, $n);
        $sig = hash_hmac('sha256', $canon, $this->secret, true);
        return [
            'X-Role-Key' => $this->keyId,
            'X-Role-Date' => (string) $ts,
            'X-Role-Nonce' => $n ?? '',
            'X-Role-Sig' => Base64Url::enc($sig),
        ];
    }
}

