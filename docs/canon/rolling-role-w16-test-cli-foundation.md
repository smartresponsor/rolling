# Rolling / Role — w16 test + CLI foundation

This wave adds the first self-testing and self-running foundation on top of the w15 canonicalized snapshot.

## Scope

- add root QA scripts in `composer.json`
- add PHPUnit, PHPStan and PHP-CS-Fixer configuration baselines
- add fixture catalog and scenario runner foundations
- add minimal `bin/console` fixture/scenario command layer
- avoid broad business rewrites or speculative Symfony bundle wiring

## Delivered command surface

- `composer test`
- `composer test:unit`
- `composer test:e2e`
- `composer lint`
- `composer stan`
- `composer cs:check`
- `composer cs:fix`
- `composer qa`
- `php bin/console app:role:fixture:list`
- `php bin/console app:role:fixture:show <name>`
- `php bin/console app:role:fixture:smoke <name>`
- `php bin/console app:role:test:scenario <name>`

## Notes

- runtime requirement is lifted to PHP `^8.4`
- `php-cs-fixer` is intentionally routed through `PHP82_BIN` for the split-runtime toolchain
- fixtures are intentionally narrow and scenario-oriented; deeper propagation/elimination semantics remain future waves
