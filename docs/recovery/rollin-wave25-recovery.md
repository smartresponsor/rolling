# Rolling wave 25 recovery

Base: current slice `RollingCS.zip` only.

## Applied fixes
- Fixed `App\Integration\Symfony\DemoPdpV2` to canonical `App\...` policy types.
- Fixed `config/services.yaml` alias drift and removed dead observability metrics resource.
- Fixed `App\Infrastructure\Acl\CachedAclSource` property drift.
- Implemented `regionForTenant()` in `App\Infrastructure\Residency\ResidencyFsPolicy`.
- Fixed `App\Service\Consistency\TokenSet::fromString()` signature/body.
- Added `App\Integration\Http\V2\Context\ContextMerge`.
- Added `App\Controller\Api\Consistency`.
- Removed transitional final-inheritance wrappers under `src/Integration/Symfony`.
- Fixed OPA / registry / HMAC / SDK test namespace drift.
- Aligned SDK PHP namespace to `Rolling\\SDK\\V2`.

## Verification
- `php -l` passed on all touched PHP files.
- `phpstan analyse src tests` passed with 0 errors in this environment.
- `php bin/console about` no longer fails on repository code; current blocker is environment preflight (`curl`, `mbstring`, Composer on PATH).
- `phpunit` cannot run green in this environment because required extensions are missing (`dom`, `mbstring`, `xml`, `xmlwriter`).
