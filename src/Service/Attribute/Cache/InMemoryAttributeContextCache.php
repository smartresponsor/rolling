<?php

declare(strict_types=1);

namespace App\Rolling\Service\Attribute\Cache;

final class InMemoryAttributeContextCache
{
    private array $m = [];

    public function set(string $k, array $val, int $ttl): void
    {
        $this->m[$k] = ['exp' => time() + $ttl, 'val' => $val];
    }

    /** @return array<string,mixed>|null */
    public function get(string $k): ?array
    {
        $r = $this->m[$k] ?? null;
        if (!$r) {
            return null;
        }
        if ($r['exp'] < time()) {
            unset($this->m[$k]);

            return null;
        }

        return $r['val'];
    }
}
