# Rolling/Role w37 - Compatibility core consolidation

This wave consolidates three remaining compatibility alias files into a single centralized file:

- `legacy_role_namespace_aliases.php`
- `legacy_role_global_aliases.php`
- `role_sdk_exception_aliases.php`

New centralized file:

- `src/Legacy/Compatibility/legacy_role_w37_aliases.php`

The consolidation preserves:

- namespace/class/interface alias continuity
- global `RoleResolver` alias continuity
- Role SDK exception alias continuity
- polyfill loading previously triggered by the global alias file

Residual structural tail remains only:

- `src/Legacy/Entity/Role`
