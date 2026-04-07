# Rolling / Role w39 — RoleResolver alias pruning

## What changed
Removed the final internal compatibility alias for `RoleResolver` from `src/Legacy/Compatibility/legacy_role_w37_aliases.php`.

## Why it was safe
A repository-wide usage scan outside `src/Legacy/Compatibility/*` found no runtime/test/bin/tool/config/src references that still require the global alias `RoleResolver`.
The only remaining code-side occurrence was the legacy class `src/Legacy/Service/RoleResolver.php` itself, plus documentation/report mentions.

## Result
- internal compatibility minimum is smaller
- centralized alias layer is cleaner
- continuity audits remain green

## Remaining structural tail
- `src/Legacy/Entity/Role`
