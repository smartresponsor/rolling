# Rolling / Role w46 — Compatibility console polyfill dedup

Removed redundant compatibility polyfill shards for Symfony Console input/output.

`legacy_role_compatibility_core.php` now loads only `symfony_console_polyfill.php`, which already defines the input/output classes guarded by `class_exists(...)` checks.

Deleted:
- `src/Legacy/Compatibility/symfony_console_input_polyfill.php`
- `src/Legacy/Compatibility/symfony_console_output_polyfill.php`

This keeps the compatibility core smaller and deterministic without changing runtime behavior.
