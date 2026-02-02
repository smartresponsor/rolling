<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace Service\Role\Security;

use src\ServiceInterface\Role\Key\KeyProviderInterface;

/**
 * HMAC-SHA256 signer with tenant-scoped keys and kid header.
 */
final class HmacSigner
{
    /**
     * @param \src\ServiceInterface\Role\Key\KeyProviderInterface $provider
     */
    public function __construct(private readonly KeyProviderInterface $provider)
    {
    }

    /** @return array{kid:string, sig:string} */
    public function sign(string $tenant, string $payload): array
    {
        $key = $this->provider->getActive($tenant);
        $raw = base64_decode($key['material'], true) ?: '';
        $sig = base64_encode(hash_hmac('sha256', $payload, $raw, true));
        return ['kid' => $key['kid'], 'sig' => $sig];
    }

    /**
     * @param string $tenant
     * @param string $payload
     * @param string $kid
     * @param string $sigB64
     * @return bool
     */
    public function verify(string $tenant, string $payload, string $kid, string $sigB64): bool
    {
        $k = $this->provider->getById($tenant, $kid);
        if (!$k) return false;
        $raw = base64_decode($k['material'], true) ?: '';
        $sig = base64_encode(hash_hmac('sha256', $payload, $raw, true));
        return hash_equals($sig, $sigB64);
    }
}
