<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\Service\Pdp\Cache;

use App\Rolling\InfrastructureInterface\Cache\CacheInterface;
use App\Rolling\ServiceInterface\Pdp\PolicyDecisionProviderInterface;

final class PdpCache implements PolicyDecisionProviderInterface
{
    public function __construct(
        private readonly PolicyDecisionProviderInterface $inner,
        private readonly CacheInterface $cache,
        private readonly int $ttlSeconds = 60,
    ) {
    }

    /**
     * @param array<string, mixed> $subject
     * @param array<string, mixed> $resource
     * @param array<string, mixed> $context
     */
    public function isAllowed(array $subject, string $action, array $resource, array $context = []): bool
    {
        $key = $this->makeKey($subject, $action, $resource, $context);
        $cached = $this->cache->get($key);
        if (null !== $cached) {
            return (bool) $cached;
        }

        $result = $this->inner->isAllowed($subject, $action, $resource, $context);
        $this->cache->set($key, $result, $this->ttlSeconds);

        return $result;
    }

    /**
     * @param array<string, mixed> $subject
     * @param array<string, mixed> $resource
     * @param array<string, mixed> $context
     */
    private function makeKey(array $subject, string $action, array $resource, array $context): string
    {
        $normalized = [
            's' => $this->ksortDeep($subject),
            'a' => $action,
            'r' => $this->ksortDeep($resource),
            'c' => $this->ksortDeep($context),
        ];

        return 'pdp:'.hash('sha256', json_encode($normalized, JSON_UNESCAPED_SLASHES) ?: '{}');
    }

    /**
     * @param array<string, mixed> $values
     *
     * @return array<string, mixed>
     */
    private function ksortDeep(array $values): array
    {
        foreach ($values as $key => $value) {
            if (is_array($value)) {
                $values[$key] = $this->ksortDeep($value);
            }
        }
        ksort($values);

        return $values;
    }
}
