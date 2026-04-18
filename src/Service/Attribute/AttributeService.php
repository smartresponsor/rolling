<?php

declare(strict_types=1);

namespace App\Service\Attribute;

<<<<<<< HEAD:src/Service/Attribute/AttributeService.php
use App\ServiceInterface\Attribute\AttributeProviderInterface;
use App\Service\Attribute\Cache\ArrayCache;
=======
use App\Attribute\Role\Cache\ArrayCache;
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Attribute/Role/AttributeService.php

/**
 *
 */

/**
 *
 */
final class AttributeService
{
    /**
     * @param array $providers
     * @param \App\Legacy\Attribute\Cache\ArrayCache|null $cache
     * @param int $ttlSec
     */
    public function __construct(private readonly array $providers, private readonly ?ArrayCache $cache = null, private readonly int $ttlSec = 30) {}

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
