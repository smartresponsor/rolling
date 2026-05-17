<?php

declare(strict_types=1);

namespace App\Rolling\Service\Cache;

final class RollingDecisionCacheInvalidator
{
    /**
     * @param RollingDecisionCache $cache
     */
    public function __construct(private readonly RollingDecisionCache $cache)
    {
    }

    /** @param array{tenant:string, subject:string, relation:string, resource:string} $tuple */
    public function onTupleWrite(array $tuple): void
    {
        $this->cache->invalidateKey($tuple['tenant'], $tuple['subject'], $tuple['relation'], $tuple['resource']);
    }
}
