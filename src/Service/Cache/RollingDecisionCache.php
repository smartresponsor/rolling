<?php

declare(strict_types=1);

namespace App\Rolling\Service\Cache;

final class RollingDecisionCache
{
    private array $store = [];
    private int $capacity;
    private int $ttlSeconds;

    public function __construct(int $capacity = 10000, int $ttlSeconds = 30)
    {
        $this->capacity = $capacity;
        $this->ttlSeconds = $ttlSeconds;
    }

    private function key(string $tenant, string $subject, string $relation, string $resource, string $mode): string
    {
        return sprintf('%s:%s:%s:%s:%s', $tenant, $subject, $relation, $resource, $mode);
    }

    public function get(string $tenant, string $subject, string $relation, string $resource, string $mode): mixed
    {
        $k = $this->key($tenant, $subject, $relation, $resource, $mode);
        if (!isset($this->store[$k])) {
            return null;
        }
        $e = $this->store[$k];
        if (time() - $e['ts'] > $this->ttlSeconds) {
            unset($this->store[$k]);

            return null;
        }

        return $e['v'];
    }

    public function set(string $tenant, string $subject, string $relation, string $resource, string $mode, mixed $value): void
    {
        if (count($this->store) >= $this->capacity) {
            array_shift($this->store); // simple eviction
        }
        $k = $this->key($tenant, $subject, $relation, $resource, $mode);
        $this->store[$k] = ['v' => $value, 'ts' => time()];
    }

    public function invalidateByPrefix(string $tenantPrefix): int
    {
        $c = 0;
        foreach (array_keys($this->store) as $k) {
            if (str_starts_with($k, $tenantPrefix.':')) {
                unset($this->store[$k]);
                ++$c;
            }
        }

        return $c;
    }

    public function invalidateKey(string $tenant, string $subject, string $relation, string $resource): void
    {
        foreach (['strong', 'eventual'] as $mode) {
            $k = sprintf('%s:%s:%s:%s:%s', $tenant, $subject, $relation, $resource, $mode);
            unset($this->store[$k]);
        }
    }
}
