# Rollin wave 28 recovery

- Added Symfony-first `App\Service\` service resource registration.
- Registered default ReBAC tuple store alias for container autowiring.
- Made `App\Service\Consistency\Composer` safe for DI by providing default token closures.
- Verified `phpstan` = 0 and `bin/console about` reaches environment preflight.
