# Rollin wave 22 recovery

## Focus

- Move embedded SDK materials out of `misc/` into root `sdk/`
- Align Composer autoload with `sdk/php/`
- Move Symfony package constraints from `^7.3` to `^8.0` only
- Keep non-component service zones in `docs/`, `tools/`, `report/`, `ops/`, `examples/`

## Structural result

- `sdk/php/` now hosts the PHP SDK and its package metadata
- `sdk/js/` now hosts JavaScript SDK assets
- `sdk/java/` now hosts Java SDK assets
- `misc/` no longer hosts SDK materials

## Notes

- `composer.lock` was not regenerated in this environment because Composer is unavailable
- Symfony `^8.0` constraints were applied to `composer.json` only
