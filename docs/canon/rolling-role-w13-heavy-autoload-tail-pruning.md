# Rolling/Role w13 — heavy autoload tail pruning

This wave narrows the remaining Composer continuity tail for legacy-heavy trees where namespace continuity is explicit enough to avoid classmap loading.

## Changes

- Replaced classmap continuity with PSR-4 continuity for:
  - `App\Infra\Role\` → `src/Legacy/Infrastructure/`
  - `App\InfraInterface\Role\` → `src/Legacy/InfrastructureInterface/`
  - `src\Security\Role\` → `src/Legacy/Security/Role/`
- Normalized one outlier namespace:
  - `App\Legacy\InfrastructureInterface\Rebac\GraphStoreInterface`
  - to `App\InfraInterface\Role\Rebac\GraphStoreInterface`
- Updated dependent import in `InMemoryGraphStore`.

## Result

The heavy classmap tail is reduced again, while canonical placement remains unchanged and forbidden trees stay quarantined in `src/Legacy/...`.
