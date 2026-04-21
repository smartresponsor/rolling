# Rolling / Role w40 — Legacy Entity Role Canonicalization

## What changed

The last structural tail under `src/Legacy/Entity/Role` was canonicalized into `src/Entity/Role`.

Canonical classes added:
- `App\Rolling\Entity\Role\PermissionKey`
- `App\Rolling\Entity\Role\Scope`
- `App\Rolling\Entity\Role\SubjectId`

Legacy classes removed:
- `src/Legacy/Entity/Role/PermissionKey.php`
- `src/Legacy/Entity/Role/Scope.php`
- `src/Legacy/Entity/Role/SubjectId.php`

Backward compatibility is preserved through:
- `src/Legacy/Compatibility/legacy_role_w40_aliases.php`

## Canon rule result

`src/Entity/Role` is now treated as the allowed entity-scoped exception.
`src/Legacy/Entity/Role` is gone.
