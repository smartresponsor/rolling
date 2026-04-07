# Rolling / Role wave w09

This wave normalizes the last non-legacy non-`App\...` namespace cluster inside canonical placement.

## Changes

- rewired `src/Exception/*.php` from `SmartResponsor\RoleSdk\V2\Exception` to `App\Exception`;
- updated canonical in-repository usage in `src/Legacy/Http/ResponseErrorMapper.php` to import `App\Exception\...`;
- added a Composer autoload files bridge at `src/Legacy/Compatibility/role_sdk_exception_aliases.php`
  so the old SDK exception FQCNs continue to resolve as aliases.

## Result

- non-legacy non-`App\...` namespace files in canonical placement: `0`;
- compatibility for old SDK exception names preserved explicitly rather than by keeping canonical files in a foreign namespace.
