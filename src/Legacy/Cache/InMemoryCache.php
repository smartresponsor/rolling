<?php

declare(strict_types=1);

namespace App\Legacy\Cache;

/**
 *
 */

/**
 *
 */
final class InMemoryCache implements KeyValueCache
{
    /** @var array */
    private array $m = [];

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed
    {
        $now = time();
        if (!isset($this->m[$key])) {
            return null;
        }
        $e = $this->m[$key];
        if ($e['exp'] !== 0 && $e['exp'] < $now) {
            unset($this->m[$key]);
            return null;
        }
        return $e['value'];
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param int $ttlSeconds
     * @return void
     */
    public function set(string $key, mixed $value, int $ttlSeconds): void
    {
        $exp = $ttlSeconds > 0 ? time() + $ttlSeconds : 0;
        $this->m[$key] = ['value' => $value, 'exp' => $exp];
    }

    /**
     * @param string $key
     * @return void
     */
    public function delete(string $key): void
    {
        unset($this->m[$key]);
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        $this->m = [];
    }
}
