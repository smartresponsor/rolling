<?php

declare(strict_types=1);

namespace App\Rolling\Service\Cache;

final class TenantCacheShardPartitioner
{
    public static function shard(string $tenant, int $shards = 64): int
    {
        return hexdec(substr(hash('xxh3', $tenant), 0, 4)) % max(1, $shards);
    }
}
