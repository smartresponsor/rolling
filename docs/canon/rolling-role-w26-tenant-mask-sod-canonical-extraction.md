# Rolling/Role w26 — Tenant / Mask / SoD canonical extraction

## Scope
- Canonical extraction of tenant, mask, and separation-of-duties service slice from `src/Legacy` to canonical `src/Service` and `src/ServiceInterface`.
- Legacy implementations on this slice reduced to bridge layer.

## Added canonical files
- `src/Service/Tenant/TenantKeyProvider.php`
- `src/Service/Tenant/SimpleTenantContextResolver.php`
- `src/Service/Mask/DataMasker.php`
- `src/Service/Sod/SodGuard.php`
- `src/ServiceInterface/Tenant/TenantKeyProviderInterface.php`
- `src/ServiceInterface/Tenant/TenantContextResolverInterface.php`

## Legacy bridge changes
- `src/Legacy/Service/Tenant/TenantKeyProvider.php`
- `src/Legacy/Service/Tenant/SimpleTenantContextResolver.php`
- `src/Legacy/Service/Mask/DataMasker.php`
- `src/Legacy/Service/Sod/SodGuard.php`
- `src/Legacy/ServiceInterface/Tenant/TenantKeyProviderInterface.php`
- `src/Legacy/ServiceInterface/Tenant/TenantContextResolverInterface.php`

## Compatibility
- Added `src/Legacy/Compatibility/legacy_role_w26_aliases.php`.

## Runtime reference updates
- `src/Controller/Api/SodController.php`
- `tool/obligation/demo.php`
- `misc/repo/tools/tenant/keygen.php`
- `misc/repo/tools/tenant/sign_verify.php`

## Result
Primary execution home for tenant/mask/sod slice is now canonical `App\Rolling\Service\...` and `App\Rolling\ServiceInterface\...`. Legacy remains BC bridge only.
