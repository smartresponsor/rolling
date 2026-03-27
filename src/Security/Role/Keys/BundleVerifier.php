<?php

declare(strict_types=1);

namespace src\Security\Role\Keys;

/**
 *
 */

/**
 *
 */
final class BundleVerifier
{
    /**
     * @param \src\Security\Role\Keys\KeyStore $keys
     */
    public function __construct(private readonly KeyStore $keys) {}

    /**
     * @param string $payload raw bytes of bundle content
     * @param string $sigB64 base64-encoded RSA-SHA256 signature
     * @param string $kid key id within JWKS
     * @param int|null $notAfter unix ts when signature expires
     */
    public function verify(string $payload, string $sigB64, string $kid, ?int $notAfter = null): bool
    {
        if ($notAfter !== null && time() > $notAfter) {
            return false; // expired
        }
        $pubPem = $this->findPublicByKid($kid);
        if ($pubPem === null) {
            return false;
        }
        $ok = openssl_verify($payload, base64_decode($sigB64), $pubPem, OPENSSL_ALGO_SHA256);
        return $ok === 1;
    }

    /**
     * @param string $kid
     * @return string|null
     */
    private function findPublicByKid(string $kid): ?string
    {
        foreach (['active', 'next'] as $slot) {
            $slotData = $this->keys->getSlot($slot);
            if ($slotData['kid'] === $kid) {
                return $slotData['public'];
            }
        }
        return null;
    }
}
