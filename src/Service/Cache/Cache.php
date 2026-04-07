<?php
declare(strict_types=1);

namespace App\Service\Cache;

/**
 *
 */

/**
 *
 */
final class Cache
{
    /** @var array */
    private array $store = [];
    private int $capacity;
    private int $ttlSeconds;

    /**
     * @param int $capacity
     * @param int $ttlSeconds
     */
    public function __construct(int $capacity = 10000, int $ttlSeconds = 30)
    {
        $this->capacity = $capacity;
        $this->ttlSeconds = $ttlSeconds;
    }

    /**
     * @param string $tenant
     * @param string $subject
     * @param string $relation
     * @param string $resource
     * @param string $mode
     * @return string
     */
    private function key(string $tenant, string $subject, string $relation, string $resource, string $mode): string
    {
        return sprintf('%s:%s:%s:%s:%s', $tenant, $subject, $relation, $resource, $mode);
    }

    /**
     * @param string $tenant
     * @param string $subject
     * @param string $relation
     * @param string $resource
     * @param string $mode
     * @return mixed
     */
    public function get(string $tenant, string $subject, string $relation, string $resource, string $mode): mixed
    {
        $k = $this->key($tenant, $subject, $relation, $resource, $mode);
        if (!isset($this->store[$k])) return null;
        $e = $this->store[$k];
        if (time() - $e['ts'] > $this->ttlSeconds) {
            unset($this->store[$k]);
            return null;
        }
        return $e['v'];
    }

    /**
     * @param string $tenant
     * @param string $subject
     * @param string $relation
     * @param string $resource
     * @param string $mode
     * @param mixed $value
     * @return void
     */
    public function set(string $tenant, string $subject, string $relation, string $resource, string $mode, mixed $value): void
    {
        if (count($this->store) >= $this->capacity) {
            array_shift($this->store); // simple eviction
        }
        $k = $this->key($tenant, $subject, $relation, $resource, $mode);
        $this->store[$k] = ['v' => $value, 'ts' => time()];
    }

    /**
     * @param string $tenantPrefix
     * @return int
     */
    public function invalidateByPrefix(string $tenantPrefix): int
    {
        $c = 0;
        foreach (array_keys($this->store) as $k) {
            if (str_starts_with($k, $tenantPrefix . ':')) {
                unset($this->store[$k]);
                $c++;
            }
        }
        return $c;
    }

    /**
     * @param string $tenant
     * @param string $subject
     * @param string $relation
     * @param string $resource
     * @return void
     */
    public function invalidateKey(string $tenant, string $subject, string $relation, string $resource): void
    {
        foreach (['strong', 'eventual'] as $mode) {
            $k = sprintf('%s:%s:%s:%s:%s', $tenant, $subject, $relation, $resource, $mode);
            unset($this->store[$k]);
        }
    }
}
