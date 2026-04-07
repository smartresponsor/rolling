# Rolling/Role w11 — legacy no-namespace tail cleanup

This wave eliminates the remaining unexpected no-namespace PHP files identified in w10.

## Changes
- added namespace to `src/Legacy/Http/Role/Role/Api/_note_consistency_dep.php`
- added namespace to `src/Legacy/Http/Role/Role/SymfonyBundle/Resources/config/services.php`
- normalized `src/Legacy/Service/RoleResolver.php` to `App\Legacy\Service\RoleResolver`
- added compatibility alias file for legacy global `RoleResolver`

## Outcome
- unexpected no-namespace legacy tail reduced to zero for the audited perimeter
- continuity preserved for the historical global `RoleResolver` symbol via `class_alias`
