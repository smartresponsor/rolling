<?php

declare(strict_types=1);

namespace App\Service\Cache;

/**
 *
 */

/**
 *
 */
final class Invalidation
{
    /**
     * @param \App\Service\Cache\Cache $cache
     */
    public function __construct(private readonly Cache $cache) {}

    /** @param array{tenant:string, subject:string, relation:string, resource:string} $tuple */
    public function onTupleWrite(array $tuple): void
    {
        $this->cache->invalidateKey($tuple['tenant'], $tuple['subject'], $tuple['relation'], $tuple['resource']);
    }
}
