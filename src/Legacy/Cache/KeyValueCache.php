<?php
declare(strict_types=1);

namespace App\Legacy\Cache;

/** Минимальный K/V кеш с TTL. */
interface KeyValueCache
{
    /** @return mixed|null */
    public function get(string $key);

    /**
     * @param string $key
     * @param mixed $value
     * @param int $ttlSeconds
     * @return void
     */
    public function set(string $key, mixed $value, int $ttlSeconds): void;

    /**
     * @param string $key
     * @return void
     */
    public function delete(string $key): void;

    /**
     * @return void
     */
    public function clear(): void;
}
