# Rolling Role w24 — audit/cache/explain/resilience extraction

This wave adds canonical `App\Service` and `App\ServiceInterface` slices for Audit, Cache, Explain, and Resilience, and switches selected controller/tool entrypoints to those canonical classes.

## Added canonical layers
- `src/Service/Audit/*`
- `src/Service/Cache/*`
- `src/Service/Explain/*`
- `src/Service/Resilience/*`
- `src/ServiceInterface/Audit/*`
- `src/ServiceInterface/Cache/*`
- `src/ServiceInterface/Resilience/*`

## Switched entrypoints
- `src/Controller/Api/CheckController.php`
- `tool/cache/*`
- `misc/repo/examples/audit/*`
- `misc/repo/tools/audit_dump.php`
- `misc/repo/tools/check_cli.php`
- `misc/repo/tools/explain_cli.php`
- `misc/repo/tools/resilience/breaker_demo.php`

## Remaining structural tail
- `src/Legacy/Entity/Role`
