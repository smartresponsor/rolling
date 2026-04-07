# Rolling / Role w54

Canonical extraction wave for remaining legacy logic in Audit, Cache, Consistency, Housekeeping, and ReBAC tuple store.

## Added canonical layers
- `App\Infrastructure\Audit\*` and `App\InfrastructureInterface\Audit\*`
- `App\Infrastructure\Cache\*`
- `App\Service\Consistency\*`
- `App\Infrastructure\Housekeeping\*`
- `App\Infrastructure\Rebac\*` and `App\InfrastructureInterface\Rebac\*`

## Internal rewiring
Selected runtime, console, tests, and docs now reference canonical App-layer classes instead of `App\Legacy\...`.

## Continuity
Legacy FQCN remain bridged through `src/Legacy/Compatibility/legacy_role_compatibility_core.php`.
