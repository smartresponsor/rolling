# Rolling W09 SOLID / canon cleanup

Rolling is a full business surface with two explicit surfaces:

- Admin surface: native EasyAdmin controllers are allowed only under `src/Controller/Admin/*`.
- Front/runtime HTTP surface: routes target `src/Service/Http/*` services directly; generic Symfony controllers are not allowed.

W09 canon decisions:

1. Generic controllers under `src/Controller/Api`, `src/Controller/V2`, `src/Controller/Observability`, and root `src/Controller/*` are removed.
2. Infrastructure contracts live under `src/InfrastructureInterface/*` and use the `Interface` suffix.
3. Infrastructure implementations stay under `src/Infrastructure/*` and must not use the `Adapter` suffix as the canonical class type.
4. GitHub subject resolution is split into an interface and a default implementation.
5. PDP cache decoration depends on `InfrastructureInterface\Cache\CacheInterface` instead of an infrastructure-local duplicate contract.
