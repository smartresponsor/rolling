# Rolling / Role — w05 leaf role collapse

This wave performs the first broad but still mechanical internal collapse of leaf `src/*/Role/...` groups.

## Intent

- remove selected forbidden `Role` paths from canonical placement;
- keep runtime continuity through explicit Composer PSR-4 mappings;
- avoid speculative business-logic rewrites and avoid mass namespace edits.

## Relocations

- `src/Store/Role/` → `src/Legacy/Store/`
- `src/Shadow/Role/` → `src/Legacy/Shadow/`
- `src/Resilience/Role/` → `src/Legacy/Resilience/`
- `src/Invalidation/Role/` → `src/Legacy/Invalidation/`
- `src/Housekeeping/Role/` → `src/Legacy/Housekeeping/`
- `src/Metrics/Role/` → `src/Legacy/Metrics/`
- `src/Observability/Role/` → `src/Legacy/Observability/`
- `src/Model/Role/` → `src/Legacy/Model/`
- `src/Net/Role/` → `src/Legacy/Net/`
- `src/Permission/Role/` → `src/Legacy/Permission/`
- `src/Entity/Role/` → `src/Legacy/Entity/Role/`


## Scope summary

- relocated groups: 11
- relocated PHP files: 34
- external root count after wave: 0
- forbidden directories in canonical placement after wave: 88
- forbidden directories held only in legacy placement after wave: 59

## Notes

This wave intentionally treats `src/Legacy/...` as a quarantine belt, not as canonical end-state architecture.
Later waves can rewrite namespaces and collapse the legacy belt once the repository is structurally safer.
