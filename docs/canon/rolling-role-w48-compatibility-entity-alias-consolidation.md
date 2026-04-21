# W48 Compatibility Entity Alias Consolidation

This wave folds the remaining entity-scoped backward-compatibility aliases into the stable compatibility core.

Changes:
- moved `App\Rolling\\Legacy\\Entity\\Role\\PermissionKey` alias into `legacy_role_compatibility_core.php`
- moved `App\Rolling\\Legacy\\Entity\\Role\\Scope` alias into `legacy_role_compatibility_core.php`
- moved `App\Rolling\\Legacy\\Entity\\Role\\SubjectId` alias into `legacy_role_compatibility_core.php`
- removed orphan wave file `legacy_role_w40_aliases.php`

Effect:
- compatibility loading stays deterministic
- no separate wave-named file remains for entity aliases
- canonical entity placement stays `src/Entity/Role/*`
