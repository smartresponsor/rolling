# Rolling / Role — w44 compatibility tail removal

This wave removes the malformed `src/Legacy/Compatibility/legacy_role_w36_aliases.php` file from the live repository base.

## Why

The file had a real PHP parse error and only represented the already-pruned internal alias tail. Keeping it in the tree risked autoload/runtime failure while adding no canonical value.

## Result

- `legacy_role_w36_aliases.php` removed
- `legacy_role_w37_aliases.php` remains as the centralized compatibility core
- SDK exception aliases and polyfill loading remain intact
- canon and continuity audits stay green
