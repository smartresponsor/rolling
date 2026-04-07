<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Service\Pdp\Cache;

use App\InfraInterface\Cache\CacheInterface;
use App\ServiceInterface\Pdp\PolicyDecisionProviderInterface;

/**
 *
 */

/**
 *
 */
final class PdpCache implements PolicyDecisionProviderInterface
{
    /**
     * @param \App\ServiceInterface\Pdp\PolicyDecisionProviderInterface $inner
     * @param \App\InfraInterface\Cache\CacheInterface $cache
     * @param int $ttlSeconds
     */
    public function __construct(
        private readonly PolicyDecisionProviderInterface $inner,
        private readonly CacheInterface                  $cache,
        private readonly int                             $ttlSeconds = 60
    )
    {
    }

    /**
     * @param array $subject
     * @param string $action
     * @param array $resource
     * @param array $context
     * @return bool
     */
    public function isAllowed(array $subject, string $action, array $resource, array $context = []): bool
    {
        $key = $this->makeKey($subject, $action, $resource, $context);
        $cached = $this->cache->get($key);
        if ($cached !== null) return (bool)$cached;

        $res = $this->inner->isAllowed($subject, $action, $resource, $context);
        $this->cache->set($key, $res, $this->ttlSeconds);
        return $res;
    }

    /**
     * @param array $subject
     * @param string $action
     * @param array $resource
     * @param array $context
     * @return string
     */
    private function makeKey(array $subject, string $action, array $resource, array $context): string
    {
        $norm = [
            's' => $this->ksortDeep($subject),
            'a' => $action,
            'r' => $this->ksortDeep($resource),
            'c' => $this->ksortDeep($context),
        ];
        return 'pdp:' . hash('sha256', json_encode($norm, JSON_UNESCAPED_SLASHES));
    }

    /**
     * @param array $a
     * @return array
     */
    private function ksortDeep(array $a): array
    {
        foreach ($a as $k => $v) {
            if (is_array($v)) $a[$k] = $this->ksortDeep($v);
        }
        ksort($a);
        return $a;
    }
}
