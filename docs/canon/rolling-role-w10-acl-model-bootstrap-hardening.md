# Rolling / Role wave w10

Base snapshot: `rolling-09-small-role-groups-collapse-cumulative.zip`

## What changed
- collapsed `src/Legacy/Acl/Role/*` into `src/Legacy/Acl/*`
- moved `RequestContext` from forbidden `src/Legacy/Domain/Role/Model` into `src/Legacy/Model/RequestContext.php`
- updated imports and references to `App\Legacy\Acl\...` and `App\Legacy\Model\RequestContext`
- added `legacy_role_alias_support.php` and `legacy_role_aliases_bootstrap.php`
- collapsed Composer `autoload.files` to a single bootstrap entry
- added `legacy_role_w10_aliases.php` for backward compatibility
- normalized `bin/no-namespace-audit.php` allowlist for intentional bootstrap/fixture entrypoints
- fixed syntax defect in `tool/policy/lint.php`

## Result
- forbidden directory count reduced to `1`
- remaining allowed exception-tail: `src/Legacy/Entity/Role`
- autoload files entries reduced to `1`
- no-namespace audit unexpected count reduced to `0`
