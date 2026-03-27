<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Service\Role\Cache;

use src\ServiceInterface\Role\Cache\TagInvalidatorInterface;

/**
 * File-cache with tag versioning for PDP-like results.
 */
final class PdpCache
{
    private string $cacheDir;
    private StampedeGuard $guard;
    private TagInvalidatorInterface $tags;

    /**
     * @param \src\ServiceInterface\Role\Cache\TagInvalidatorInterface $tags
     * @param \App\Service\Role\Cache\StampedeGuard|null $guard
     * @param string $cacheDir
     */
    public function __construct(TagInvalidatorInterface $tags, ?StampedeGuard $guard = null, string $cacheDir = '/tmp/role_cache')
    {
        $this->cacheDir = $cacheDir;
        $this->guard = $guard ?? new StampedeGuard();
        $this->tags = $tags;
        if (!is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, 0775, true);
        }
    }

    /**
     * @param array $keyParts
     * @param int $ttlMs
     * @param string[] $tags
     * @param callable $producer
     * @return mixed
     */
    public function get(array $keyParts, int $ttlMs, array $tags, callable $producer)
    {
        $tagVer = [];
        foreach ($tags as $t) {
            $tagVer[$t] = $this->tags->getTagVersion($t);
        }
        $rawKey = json_encode([$keyParts, $tagVer], JSON_UNESCAPED_SLASHES);
        $hash = sha1($rawKey);
        $path = $this->cacheDir . '/' . $hash . '.json';

        // Read
        if (is_file($path)) {
            $d = json_decode((string) file_get_contents($path), true);
            if (is_array($d) && isset($d['expiresAt']) && $d['expiresAt'] > (int) (microtime(true) * 1000)) {
                return $d['value'];
            }
        }

        // Compute with stampede guard
        $res = $this->guard->computeWithLock($hash, $ttlMs, $producer);
        @file_put_contents($path, json_encode($res));
        return $res['value'];
    }
}
