# Rolling/Role w04 internal collapse

This wave removes the most explicit forbidden canonical placements while preserving runtime continuity.

## Moves

- `src/Domain/Role/` -> `src/Legacy/Domain/Role/`
- `src/Acl/Role/Adapter/` -> `src/Legacy/Acl/Role/Adapter/`

## Continuity

Composer PSR-4 mappings were extended so existing namespaces continue to resolve without a mass namespace rewrite.
