<?php

declare(strict_types=1);

namespace App\Rolling\Security\Hmac;

use App\Rolling\Security\Util\Base64Url;

final class Signer
{
    public function __construct(private readonly string $keyId, private readonly string $secret)
    {
    }

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
