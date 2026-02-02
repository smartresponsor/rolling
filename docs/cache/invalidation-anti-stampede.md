# Cache Invalidation + Anti-Stampede (G2)

- `TagInvalidatorInterface` + file-based implementation (`TagInvalidator`).
- `StampedeGuard` with file lock + TTL jitter (reduce TTL by up to 15%).
- `PdpCache` that composes tag versions into cache keys and guards recomputation.

## Demos

- `php tools/cache/invalidate_demo.php` → `report/cache_invalidate_demo.json`  
  Expected: first==second, after_invalidate!=second.
- `php tools/cache/stampede_demo.php` → `report/cache_stampede_demo.json`  
  Expected: all_equal=true; elapsedMs close to a single producer run.
