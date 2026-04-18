<?php

declare(strict_types=1);

namespace App\Security\Hmac;

use App\Security\Util\Base64Url;

/**
 *
 */

/**
 *
 */
interface SecretProviderInterface
{
    /**
     * @param string $keyId
     * @return string|null
     */
    public function secret(string $keyId): ?string;
}

/**
 *
 */

/**
 *
 */
final class InMemorySecretProvider implements \App\Security\Role\Hmac\SecretProviderInterface
{
    /**
     * @param array $map
     */
    public function __construct(private readonly array $map) {}

    /**
     * @param string $keyId
     * @return string|null
     */
    public function secret(string $keyId): ?string
    {
        return $this->map[$keyId] ?? null;
    }
}

/**
 *
 */

/**
 *
 */
interface NonceStoreInterface
{
    /**
     * @param string $nonce
     * @param int $ttlSec
     * @return bool
     */
    public function seen(string $nonce, int $ttlSec): bool;
}

/**
 *
 */

/**
 *
 */
final class InMemoryNonceStore implements NonceStoreInterface
{
    private array $exp = [];

    /**
     * @param string $nonce
     * @param int $ttlSec
     * @return bool
     */
    public function seen(string $nonce, int $ttlSec): bool
    {
        $now = time();
        foreach ($this->exp as $n => $e) {
            if ($e < $now) {
                unset($this->exp[$n]);
            }
        }
        if ($nonce === '') {
            return false;
        }
        if (isset($this->exp[$nonce]) && $this->exp[$nonce] >= $now) {
            return true;
        }
        $this->exp[$nonce] = $now + $ttlSec;
        return false;
    }
}

/**
 *
 */

/**
 *
 */
final class Verifier
{
    /**
     * @param \App\Security\Hmac\SecretProviderInterface $secrets
     * @param \App\Security\Hmac\NonceStoreInterface|null $nonces
     * @param int $maxSkewSec
     */
    public function __construct(private readonly SecretProviderInterface $secrets, private readonly ?NonceStoreInterface $nonces = null, private readonly int $maxSkewSec = 300) {}

    /**
     * @param string $method
     * @param string $pathWithQuery
     * @param string $body
     * @param array $headers
     * @return bool
     */
    public function verify(string $method, string $pathWithQuery, string $body, array $headers): bool
    {
        $kid = $headers['x-role-key'] ?? '';
        $ts = (int) ($headers['x-role-date'] ?? '0');
        $sig = $headers['x-role-sig'] ?? '';
        $nonce = $headers['x-role-nonce'] ?? '';
        if ($kid === '' || $ts === 0 || $sig === '') {
            return false;
        }
        if (abs(time() - $ts) > $this->maxSkewSec) {
            return false;
        }
        if ($this->nonces && $nonce !== '' && $this->nonces->seen($nonce, 300)) {
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
