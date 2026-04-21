# Rolling / Role — Wave 15

## Scope
- Extract HTTP security verifier into canonical `src/Security/Http`
- Extract replay nonce contract and PDO implementation into canonical `src/InfrastructureInterface` / `src/Infrastructure`
- Extract Symfony request subscriber and bundle wiring into canonical `src/Infrastructure/Symfony`
- Keep legacy and integration classes as bridge-zone wrappers

## Canonical additions
- `App\Rolling\Security\Http\HmacRequestVerifier`
- `App\Rolling\InfrastructureInterface\Security\ReplayNonceStoreInterface`
- `App\Rolling\Infrastructure\Security\Replay\PdoReplayNonceStore`
- `App\Rolling\Infrastructure\Symfony\EventSubscriber\HmacGuardSubscriber`
- `App\Rolling\Infrastructure\Symfony\DependencyInjection\Configuration`
- `App\Rolling\Infrastructure\Symfony\DependencyInjection\RoleExtension`
- `App\Rolling\Infrastructure\Symfony\RoleBundle`

## Bridge-zone changes
- `App\Rolling\Legacy\Http\Security\HmacVerifier` now extends canonical verifier
- `App\Rolling\Legacy\Http\Security\Replay\StoreInterface` now extends canonical replay contract
- `App\Rolling\Legacy\Http\Security\Replay\PdoStore` now extends canonical PDO replay store
- `App\Rolling\Integration\Symfony\DependencyInjection\Configuration` bridges to canonical infrastructure configuration
- `App\Rolling\Integration\Symfony\DependencyInjection\RoleExtension` bridges to canonical infrastructure extension
- `App\Rolling\Integration\Symfony\EventSubscriber\HmacGuardSubscriber` bridges to canonical infrastructure subscriber
- `App\Rolling\Integration\Symfony\RoleBundle\RoleBundle` bridges to canonical infrastructure bundle

## Runtime effect
Primary home for Symfony HTTP security wiring is now canonical `App\Rolling\Security\...`, `App\Rolling\Infrastructure\...`, and `App\Rolling\InfrastructureInterface\...`.
