<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Infrastructure\Security;

use App\InfrastructureInterface\Security\{KeyStoreInterface, JwksVerifierInterface};

/**
 *
 */

/**
 *
 */
final class JwksFsVerifier implements JwksVerifierInterface
{
    /**
     * @param \App\InfrastructureInterface\Security\KeyStoreInterface $store
     */
    public function __construct(private readonly KeyStoreInterface $store)
    {
    }

    /**
     * @param string $tenant
     * @param string $jwt
     * @return array
     */
    public function verify(string $tenant, string $jwt): array
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return ['ok' => false, 'header' => [], 'payload' => [], 'kid' => null];
        }
        [$h, $p, $s] = $parts;
        $header = json_decode(JoseUtil::b64urld($h), true) ?: [];
        $payload = json_decode(JoseUtil::b64urld($p), true) ?: [];
        $alg = (string) ($header['alg'] ?? '');
        $kid = (string) ($header['kid'] ?? '');
        $data = $h . '.' . $p;
        if ($alg === 'HS256') {
            $key = $kid ? $this->store->loadHmac($tenant, $kid) : $this->store->currentHmac($tenant)['key'];
            if ($key === null) {
                return ['ok' => false, 'header' => $header, 'payload' => $payload, 'kid' => $kid];
            }
            $calc = JoseUtil::b64url(JoseUtil::hmac256($data, $key));
            return ['ok' => hash_equals($calc, $s), 'header' => $header, 'payload' => $payload, 'kid' => $kid ?: $this->store->currentHmac($tenant)['kid']];
        }
        if ($alg === 'RS256') {
            $jwks = $this->store->jwks($tenant);
            foreach ((array) ($jwks['keys'] ?? []) as $k) {
                if (($k['kid'] ?? '') === $kid && isset($k['pem'])) {
                    return ['ok' => JoseUtil::rs256_verify($data, $s, (string) $k['pem']), 'header' => $header, 'payload' => $payload, 'kid' => $kid];
                }
            }
            return ['ok' => false, 'header' => $header, 'payload' => $payload, 'kid' => $kid];
        }
        return ['ok' => false, 'header' => $header, 'payload' => $payload, 'kid' => $kid];
    }

    /**
     * @param string $tenant
     * @param array $payload
     * @param string|null $kid
     * @return string
     */
    public function signHs256(string $tenant, array $payload, ?string $kid = null): string
    {
        $cur = $this->store->currentHmac($tenant);
        $kid = $kid ?: $cur['kid'];
        $header = ['alg' => 'HS256', 'typ' => 'JWT', 'kid' => $kid];
        $h = JoseUtil::b64url(json_encode($header, JSON_UNESCAPED_SLASHES));
        $p = JoseUtil::b64url(json_encode($payload, JSON_UNESCAPED_SLASHES));
        $sig = JoseUtil::b64url(JoseUtil::hmac256($h . '.' . $p, $cur['key']));
        return $h . '.' . $p . '.' . $sig;
    }
}
