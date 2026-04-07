# Rolling / Role — w12 autoload continuity pruning

This wave tightens Composer autoload continuity without returning any forbidden placement into canonical `src/`.

## Scope

- fix the broken SDK PSR-4 path in root `composer.json`
- replace selected legacy `classmap` exceptions with narrower PSR-4 continuity mappings where namespaces are already mechanically aligned
- add a repeatable autoload continuity audit script and report

## Safe reductions performed

The following legacy groups are now resolved via explicit PSR-4 continuity instead of broad classmap fallback:

- `App\Acl\Role\` → `src/Legacy/Acl/Role/`
- `App\Legacy\Attribute\` → `src/Legacy/Attribute/`
- `App\Legacy\Audit\` → `src/Legacy/Audit/`
- `App\Legacy\Cache\` → `src/Legacy/Cache/`
- `App\Legacy\Consistency\` → `src/Legacy/Consistency/`

## Result

- broken Composer autoload path count is expected to be zero
- legacy classmap footprint is reduced
- legacy continuity remains explicit and reviewable
