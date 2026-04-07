# Rolling/Role wave w07 — Policy and Security tree collapse

Base slice: `rolling-06-infrastructure-tree-collapse-cumulative.zip`

## What changed
- Collapsed `src/Legacy/Policy/Role/*` into `src/Legacy/Policy/*`
- Collapsed `src/Legacy/PolicyInterface/Role/*` into `src/Legacy/PolicyInterface/*`
- Collapsed `src/Legacy/Security/Role/*` into `src/Legacy/Security/*`
- Rewrote namespaces from `App\Legacy\...\Role\...` to `App\Legacy\...\...`
- Added continuity aliases in `src/Legacy/Compatibility/legacy_role_w07_aliases.php`

## Verification
- `php bin/php-lint-all.php` — pass
- `php bin/autoload-continuity-audit.php` — pass
- `php bin/namespace-audit.php` — pass

## Structural effect
This wave removes three more competing `.../Role/...` subtrees from the active legacy code path while preserving backward compatibility for old FQCN consumers.
