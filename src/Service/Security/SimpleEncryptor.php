<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\Service\Security;

use App\Rolling\ServiceInterface\Key\KeyProviderInterface;

/**
 * AES-256-GCM wrapper (requires openssl). Nonce length 12 bytes.
 */
final class SimpleEncryptor
{
    /**
     * @param KeyProviderInterface $provider
     */
    public function __construct(private readonly KeyProviderInterface $provider)
    {
    }

    /**
     * @param string $tenant
     * @param string $plain
     *
     * @return array{kid:string, iv:string, ct:string, tag:string}
     */
    public function encrypt(string $tenant, string $plain): array
    {
        $k = $this->provider->getActive($tenant);
        $key = base64_decode($k['material'], true) ?: '';
        try {
            $iv = random_bytes(12);
        } catch (\Exception $e) {
            error_log('SimpleEncryptor::encrypt iv fallback: '.$e->getMessage());
            $iv = substr(hash('sha256', $tenant.'|'.$plain.'|'.microtime(true), true), 0, 12);
        }
        $tag = '';
        $ct = openssl_encrypt($plain, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);

        return ['kid' => $k['kid'], 'iv' => base64_encode($iv), 'ct' => base64_encode($ct ?: ''), 'tag' => base64_encode($tag)];
    }

    /**
     * @param string $tenant
     * @param string $kid
     * @param string $ivB64
     * @param string $ctB64
     * @param string $tagB64
     *
     * @return string|null
     */
    public function decrypt(string $tenant, string $kid, string $ivB64, string $ctB64, string $tagB64): ?string
    {
        $k = $this->provider->getById($tenant, $kid);
        if (!$k) {
            return null;
        }
        $key = base64_decode($k['material'], true) ?: '';
        $iv = base64_decode($ivB64, true) ?: '';
        $ct = base64_decode($ctB64, true) ?: '';
        $tag = base64_decode($tagB64, true) ?: '';
        $pt = openssl_decrypt($ct, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);

        return false === $pt ? null : $pt;
    }
}
