# Rolling / Role — Wave 15

## Scope
- Extract HTTP security verifier into canonical `src/Security/Http`
- Extract replay nonce contract and PDO implementation into canonical `src/InfrastructureInterface` / `src/Infrastructure`
- Extract Symfony request subscriber and bundle wiring into canonical `src/Infrastructure/Symfony`
- Keep legacy and integration classes as bridge-zone wrappers

## Canonical additions
- `App\Security\Http\HmacRequestVerifier`
- `App\InfrastructureInterface\Security\ReplayNonceStoreInterface`
- `App\Infrastructure\Security\Replay\PdoReplayNonceStore`
- `App\Infrastructure\Symfony\EventSubscriber\HmacGuardSubscriber`
- `App\Infrastructure\Symfony\DependencyInjection\Configuration`
- `App\Infrastructure\Symfony\DependencyInjection\RoleExtension`
- `App\Infrastructure\Symfony\RoleBundle`

## Bridge-zone changes
- `App\Legacy\Http\Security\HmacVerifier` now extends canonical verifier
- `App\Legacy\Http\Security\Replay\StoreInterface` now extends canonical replay contract
- `App\Legacy\Http\Security\Replay\PdoStore` now extends canonical PDO replay store
- `App\Integration\Symfony\DependencyInjection\Configuration` bridges to canonical infrastructure configuration
- `App\Integration\Symfony\DependencyInjection\RoleExtension` bridges to canonical infrastructure extension
- `App\Integration\Symfony\EventSubscriber\HmacGuardSubscriber` bridges to canonical infrastructure subscriber
- `App\Integration\Symfony\RoleBundle\RoleBundle` bridges to canonical infrastructure bundle

## Runtime effect
Primary home for Symfony HTTP security wiring is now canonical `App\Security\...`, `App\Infrastructure\...`, and `App\InfrastructureInterface\...`.
