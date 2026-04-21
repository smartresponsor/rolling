<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Cache;

/**
 * Обёртка над PSR-16 (если он есть в проекте). Без жёсткой зависимости в сигнатуре.
 *
 * @psalm-type Psr16 = \Psr\SimpleCache\CacheInterface
 */
final class Psr16CacheAdapter implements KeyValueCache
{
    /** @var object */
    private object $psr;

    /** @param object $psr Должен реализовывать Psr\SimpleCache\CacheInterface */
    public function __construct(object $psr)
    {
        $this->psr = $psr;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key): mixed
    {
        if (!method_exists($this->psr, 'get')) {
            return null;
        }

        /** @var mixed */
        return $this->psr->get($key);
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @param int    $ttlSeconds
     *
     * @return void
     */
    public function set(string $key, mixed $value, int $ttlSeconds): void
    {
        if (!method_exists($this->psr, 'set')) {
            return;
        }
        $ttl = $ttlSeconds > 0 ? $ttlSeconds : null;
        $this->psr->set($key, $value, $ttl);
    }

    /**
     * @param string $key
     *
     * @return void
     */
    public function delete(string $key): void
    {
        if (method_exists($this->psr, 'delete')) {
            $this->psr->delete($key);
        }
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        if (method_exists($this->psr, 'clear')) {
            $this->psr->clear();
        }
    }
}
