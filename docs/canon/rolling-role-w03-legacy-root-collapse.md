# Rolling/Role w03 — legacy root collapse into src/

## Scope

This wave performs the first structural collapse of non-canonical production roots.

## Goal

Remove parallel production roots from repository root and keep the codebase under a single `src/` production tree boundary.

## Applied changes

Moved the following legacy roots under `src/Legacy/` without semantic rewrites:

- `Http/` -> `src/Legacy/Http/`
- `Policy/` -> `src/Legacy/Policy/`
- `PolicyInterface/` -> `src/Legacy/PolicyInterface/`
- `Service/` -> `src/Legacy/Service/`

Updated `composer.json` PSR-4 map accordingly:

- `Http\\` -> `src/Legacy/Http/`
- `Policy\\` -> `src/Legacy/Policy/`
- `PolicyInterface\\` -> `src/Legacy/PolicyInterface/`
- `Service\\` -> `src/Legacy/Service/`

## Intentionally not done

- No mass namespace rewrite to `App\Rolling\\...`
- No collapse of `.../Role/...` directories yet
- No `Domain` / `Port` elimination yet
- No business-logic rewiring

## Result

After this wave, repository-root competing production trees are eliminated. Remaining canon drift is now concentrated inside `src/` and can be processed in subsequent waves.
