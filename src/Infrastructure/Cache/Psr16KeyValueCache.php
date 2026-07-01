<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Cache;

use App\Rolling\InfrastructureInterface\Cache\CacheInterface;

/**
 * PSR-16 backed key/value cache implementation.
 *
 * @psalm-type Psr16 = \Psr\SimpleCache\CacheInterface
 */
final class Psr16KeyValueCache implements CacheInterface
{
    private object $psr;

    /**
     * @param object $psr expected to implement Psr\SimpleCache\CacheInterface
     */
    public function __construct(object $psr)
    {
        $this->psr = $psr;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if (!method_exists($this->psr, 'get')) {
            return $default;
        }

        return $this->psr->get($key, $default);
    }

    public function set(string $key, mixed $value, int $ttl): bool
    {
        if (!method_exists($this->psr, 'set')) {
            return false;
        }

        return (bool) $this->psr->set($key, $value, $ttl > 0 ? $ttl : null);
    }

    public function delete(string $key): bool
    {
        if (!method_exists($this->psr, 'delete')) {
            return false;
        }

        return (bool) $this->psr->delete($key);
    }

    public function clear(): bool
    {
        if (!method_exists($this->psr, 'clear')) {
            return false;
        }

        return (bool) $this->psr->clear();
    }
}
