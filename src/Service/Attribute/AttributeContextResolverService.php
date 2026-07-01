<?php

declare(strict_types=1);

namespace App\Rolling\Service\Attribute;

use App\Rolling\Service\Attribute\Cache\InMemoryAttributeContextCache;

final class AttributeContextResolverService
{
    public function __construct(private readonly array $providers, private readonly ?InMemoryAttributeContextCache $cache = null, private readonly int $ttlSec = 30)
    {
    }

    /** @return array<string,mixed> */
    public function user(string $id): array
    {
        $key = "u:$id";
        if ($this->cache && ($v = $this->cache->get($key))) {
            return $v;
        }
        $ctx = [];
        foreach ($this->providers as $p) {
            $ctx += $p->forUser($id);
        }
        $this->cache?->set($key, $ctx, $this->ttlSec);

        return $ctx;
    }

    /** @return array<string,mixed> */
    public function org(string $id): array
    {
        $key = "o:$id";
        if ($this->cache && ($v = $this->cache->get($key))) {
            return $v;
        }
        $ctx = [];
        foreach ($this->providers as $p) {
            $ctx += $p->forOrg($id);
        }
        $this->cache?->set($key, $ctx, $this->ttlSec);

        return $ctx;
    }

    /** @return array<string,mixed> */
    public function resource(string $id): array
    {
        $key = "r:$id";
        if ($this->cache && ($v = $this->cache->get($key))) {
            return $v;
        }
        $ctx = [];
        foreach ($this->providers as $p) {
            $ctx += $p->forResource($id);
        }
        $this->cache?->set($key, $ctx, $this->ttlSec);

        return $ctx;
    }
}
