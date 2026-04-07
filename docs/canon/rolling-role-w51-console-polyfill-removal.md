# rolling-role-w51-console-polyfill-removal

Removed the last surviving internal compatibility edge for Symfony Console.

Changes:
- deleted `src/Legacy/Compatibility/symfony_console_polyfill.php`
- deleted `src/Legacy/Compatibility/legacy_role_compatibility_core.php`
- simplified `src/Legacy/Compatibility/legacy_role_aliases_bootstrap.php` to load only `legacy_role_alias_support.php`

Why this is safe:
- `composer.json` already requires `symfony/console`
- internal code uses the real Symfony Console classes from the package
- no repository-internal compatibility aliases remain
