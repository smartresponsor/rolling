# Rolling/Role w50 — Compatibility core minimum

Removed internally unused entity, obligation, and SDK exception aliases from `src/Legacy/Compatibility/legacy_role_compatibility_core.php`.

The surviving compatibility core now only requires `symfony_console_polyfill.php` and no longer carries repository-internal BC mappings.
