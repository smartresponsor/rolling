# PDP Cache Decorator

- Interface: `ServiceInterface/Role/Pdp/PolicyDecisionProviderInterface`
- Decorator: `Service/Pdp/Cache/CachedPolicyDecisionProvider`
- Cache: `InfraInterface/Cache/CacheInterface` + `Infra/Cache/InMemoryCache`

## Run smoke

```
php tools/pdp/cache_smoke.php
cat report/pdp_cache_smoke.json
```
