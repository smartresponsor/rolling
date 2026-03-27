<?php

declare(strict_types=1);

namespace src\Security\Role\Policy;

use src\Security\Role\Util\Base64Url;

/**
 *
 */

/**
 *
 */
interface KeyProviderInterface
{
    /**
     * @param string $kid
     * @return string|null
     */
    public function publicPem(string $kid): ?string;
}

/**
 *
 */

/**
 *
 */
final class FileKeyProvider implements KeyProviderInterface
{
    /**
     * @param array $map
     */
    public function __construct(private readonly array $map) {}

    /**
     * @param string $kid
     * @return string|null
     */
    public function publicPem(string $kid): ?string
    {
        $p = $this->map[$kid] ?? null;
        if (!$p || !is_file($p)) {
            return null;
        }
        return file_get_contents($p) ?: null;
    }
}

/**
 *
 */

/**
 *
 */
final class PolicyVerifier
{
    /**
     * @param \src\Security\Role\Policy\KeyProviderInterface $keys
     */
    public function __construct(private readonly KeyProviderInterface $keys) {}

    /**
     * @param array $bundle
     * @param int $maxSkewSec
     * @return bool
     */
    public function verify(array $bundle, int $maxSkewSec = 300): bool
    {
        foreach (['alg', 'kid', 'ts', 'hash', 'sig', 'doc'] as $k) {
            if (!array_key_exists($k, $bundle)) {
                return false;
            }
        }
        $alg = (string) $bundle['alg'];
        $kid = (string) $bundle['kid'];
        $ts = (int) $bundle['ts'];
        if (abs(time() - $ts) > $maxSkewSec) {
            return false;
        }
        $sig = Base64Url::dec((string) $bundle['sig']);
        $hash = (string) $bundle['hash'];
        if (!str_starts_with($hash, 'sha256:')) {
            return false;
        }
        $docJson = json_encode($bundle['doc'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
        $calc = 'sha256:' . hash('sha256', $docJson);
        if (!hash_equals($hash, $calc)) {
            return false;
        }
        $payload = $alg . '|' . $kid . '|' . $ts . '|' . substr($calc, 7);
        $pub = $this->keys->publicPem($kid);
        if (!$pub) {
            return false;
        }
        return openssl_verify($payload, $sig, $pub, OPENSSL_ALGO_SHA256) === 1;
    }
}
