<?php

declare(strict_types=1);

namespace App\Rolling\Security\Hmac;

use App\Rolling\Security\Util\Base64Url;

final class Verifier
{
    public function __construct(private readonly SecretProviderInterface $secrets, private readonly ?NonceStoreInterface $nonces = null, private readonly int $maxSkewSec = 300)
    {
    }

    public function verify(string $method, string $pathWithQuery, string $body, array $headers): bool
    {
        $kid = $headers['x-role-key'] ?? '';
        $ts = (int) ($headers['x-role-date'] ?? '0');
        $sig = $headers['x-role-sig'] ?? '';
        $nonce = $headers['x-role-nonce'] ?? '';
        if ('' === $kid || 0 === $ts || '' === $sig) {
            return false;
        }
        if (abs(time() - $ts) > $this->maxSkewSec) {
            return false;
        }
        if ($this->nonces && '' !== $nonce && $this->nonces->seen($nonce, 300)) {
            return false;
        }
        $secret = $this->secrets->secret($kid);
        if (!$secret) {
            return false;
        }
        $canon = Canonicalizer::canonical($method, $pathWithQuery, $body, $ts, $nonce);
        $calc = Base64Url::enc(hash_hmac('sha256', $canon, $secret, true));

        return hash_equals($calc, $sig);
    }
}
