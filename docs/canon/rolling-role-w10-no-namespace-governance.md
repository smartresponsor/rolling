# Rolling/Role w10 — no-namespace governance

This wave normalizes the low-risk no-namespace perimeter without forcing a dangerous mass rewrite of executable entrypoints.

## Changes

- Added `autoload-dev` mapping for `App\Tests\` -> `tests/`.
- Normalized `tests/Role/Model/DiffTest.php` into `App\Tests\Role\Model`.
- Namespaced the internal audit executables:
  - `bin/canon-scan.php`
  - `bin/namespace-audit.php`
- Added `bin/no-namespace-audit.php` to classify intentional global-scope entrypoints versus unexpected no-namespace files.

## Intentional global-scope files

The following remain intentionally global in this wave:

- CLI entrypoints under `bin/role-*.php`
- compatibility alias bootstrap `src/Legacy/Compatibility/role_sdk_exception_aliases.php`

They can be revisited later, but are intentionally not mass-normalized in this wave.
