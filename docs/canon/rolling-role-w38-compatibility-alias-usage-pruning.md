# Rolling / Role w38

This wave prunes unused internal compatibility aliases from `src/Legacy/Compatibility/legacy_role_w37_aliases.php`.

## Result

- Removed 76 alias mappings that have zero occurrences in the repository outside `src/Legacy/Compatibility/*`.
- Kept only the live internal alias `RoleResolver`.
- Left polyfill loading untouched.
- Left the allowed entity-scoped tail `src/Legacy/Entity/Role` untouched.

## Verification

- `php bin/autoload-continuity-audit.php` — pass
- `php bin/namespace-audit.php` — pass
- `php bin/no-namespace-audit.php` — pass
- `php bin/canon-scan.php` — pass
