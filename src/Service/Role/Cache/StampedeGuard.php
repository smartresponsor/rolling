<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Service\Role\Cache;

/**
 * File-lock based stampede guard with TTL jitter.
 */
final class StampedeGuard
{
    /**
     * @param string $lockDir
     * @param int $jitterPercent
     */
    public function __construct(
        private readonly string $lockDir = '/tmp/role_cache_locks',
        private readonly int    $jitterPercent = 15
    )
    {
        if (!is_dir($this->lockDir)) @mkdir($this->lockDir, 0775, true);
    }

    /**
     * @template T
     * @param string $key
     * @param int $ttlMs
     * @param callable():T $producer
     * @return array{value:mixed, ttlMs:int, expiresAt:int}
     */
    public function computeWithLock(string $key, int $ttlMs, callable $producer): array
    {
        $lockPath = $this->lockDir . '/' . sha1($key) . '.lock';
        $fp = fopen($lockPath, 'c');
        if ($fp === false) {
            // fallback: compute without lock
            $value = $producer();
            $effectiveTtl = $this->applyJitter($ttlMs);
            return ['value' => $value, 'ttlMs' => $effectiveTtl, 'expiresAt' => (int)(microtime(true) * 1000) + $effectiveTtl];
        }
        $locked = flock($fp, LOCK_EX);
        try {
            $value = $producer();
        } finally {
            if ($locked) flock($fp, LOCK_UN);
            fclose($fp);
        }
        $effectiveTtl = $this->applyJitter($ttlMs);
        return ['value' => $value, 'ttlMs' => $effectiveTtl, 'expiresAt' => (int)(microtime(true) * 1000) + $effectiveTtl];
    }

    /**
     * @param int $ttlMs
     * @return int
     */
    private function applyJitter(int $ttlMs): int
    {
        $p = max(0, min(90, $this->jitterPercent));
        $delta = (int)floor($ttlMs * $p / 100);
        try {
            $j = random_int(0, $delta);
        } catch (\Exception $e) {
        }
        return $ttlMs - $j; // only reduce TTL (anti-stampede)
    }
}
