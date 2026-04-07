# Rolling/Role w07 final canonical role collapse

This wave completes the internal structural quarantine of the remaining forbidden canonical `src/*/Role/...` groups.

## Relocations
- `src/Acl/Role/` -> `src/Legacy/Acl/Role/`
- `src/Attribute/Role/` -> `src/Legacy/Attribute/`
- `src/Audit/Role/` -> `src/Legacy/Audit/`
- `src/Cache/Role/` -> `src/Legacy/Cache/`
- `src/Consistency/Role/` -> `src/Legacy/Consistency/`
- `src/Security/Role/` -> `src/Legacy/Security/Role/`

## Outcome

- Remaining canonical forbidden directories after w07: `0`
- Legacy-only forbidden directories after w07: `144`
- External roots outside `src/` after w07: `0`
- Relocated PHP files in the moved groups: `40`
