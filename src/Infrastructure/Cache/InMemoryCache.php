<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\Infrastructure\Cache;

use App\Rolling\InfrastructureInterface\Cache\CacheInterface;

final class InMemoryCache implements CacheInterface
{
    /** @var array */
    private array $data = [];

    /**
     * @param string     $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $now = time();
        if (isset($this->data[$key]) && $this->data[$key]['exp'] >= $now) {
            return $this->data[$key]['v'];
        }

        return $default;
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @param int    $ttl
     *
     * @return bool
     */
    public function set(string $key, mixed $value, int $ttl): bool
    {
        $this->data[$key] = ['v' => $value, 'exp' => time() + max(0, $ttl)];

        return true;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function delete(string $key): bool
    {
        unset($this->data[$key]);

        return true;
    }
}
