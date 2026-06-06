# Rolling / Role w16 — integration bridge thinning

This wave thins remaining donor-zone integration classes by pointing them at canonical App-layer classes.

## Changes

- canonical `App\Rolling\PolicyInterface\PdpV2Interface` adopted by runtime-facing controllers, console runtimes, bins, and selected tests
- `App\Rolling\Integration\Symfony\Controller\RoleApiV2Controller` now bridges to `App\Rolling\Service\Http\Role\V2\AccessHttpService`
- `App\Rolling\Integration\Symfony\Controller\MetricsController` now bridges to `App\Rolling\Service\Http\Role\Observability\MetricsHttpService`
- `App\Rolling\Legacy\Http\SymfonyBundle\*` bundle/DI classes now bridge to canonical `App\Rolling\Infrastructure\Symfony\*`
- integration docs updated to canonical bundle/controller naming

## Intent

Reduce donor-zone execution weight while preserving backward-compatible entrypoints through alias/bootstrap continuity.
