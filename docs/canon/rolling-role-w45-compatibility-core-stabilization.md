# Rolling / Role w45 — Compatibility Core Stabilization

This wave replaces the last wave-named compatibility core file with a stable deterministic compatibility core.

## Changes
- Renamed `legacy_role_w37_aliases.php` to `legacy_role_compatibility_core.php`
- Simplified `legacy_role_aliases_bootstrap.php` to explicit deterministic loading
- Removed duplicate `legacy_role_alias_support.php` include from the compatibility core

## Result
The compatibility layer no longer depends on wave-numbered file naming for its surviving minimal external BC surface.
