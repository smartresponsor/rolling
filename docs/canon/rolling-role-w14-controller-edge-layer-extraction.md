# Rolling / Role w14 — controller edge-layer extraction

This wave extracts real HTTP and Symfony controller entrypoints from legacy and integration locations into canonical Symfony-oriented controller layers under `src/Controller`.

## Added canonical controllers
- `src/Controller/Api/*`
- `src/Controller/Api/Admin/TenantAdminController.php`
- `src/Controller/V2/*`
- `src/Controller/Observability/MetricsController.php`

## Routing changes
Route YAML entries were switched from `App\Http\Role\...` and `App\Integration\Symfony\Controller\...` to `App\Controller\...`.

## Continuity
A new compatibility alias file keeps old controller FQCNs resolving to the new canonical classes.
