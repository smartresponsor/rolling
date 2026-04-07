# Rolling/Role w08 — namespace continuity baseline

This wave does not reopen forbidden canonical placements and does not attempt a mass namespace rewrite.

## Purpose

`w08` establishes a repeatable namespace audit baseline so later waves can normalize namespaces from facts rather than assumptions.

## Added controls

- root Composer scripts:
  - `composer canon:scan`
  - `composer canon:namespace-audit`
- `bin/namespace-audit.php`
- machine-readable report: `report/rolling-role-w08-namespace-audit.json`

## Key findings at w08 baseline

- canonical forbidden placement remains **0**
- the codebase still contains a large legacy namespace surface under `src/Legacy/...`
- non-`App\` namespaces are now explicitly inventoried for stepwise cleanup
- files without namespace declarations are separately tracked

## Intent for next waves

- first normalize non-legacy production namespaces that are still outside `App\`
- then reduce legacy namespace drift in bounded groups
- only after that consider deeper relocation or semantic refactors
