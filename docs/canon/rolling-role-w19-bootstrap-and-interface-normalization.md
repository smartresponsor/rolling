# Rolling / Role w19 — bootstrap and interface normalization

## What changed
- Switched legacy alias bootstrap from a hard-coded file list to a dynamic `*aliases.php` loader.
- Normalized `src/ServiceInterface/ObligationStoreInterface.php` to the canonical namespace `App\Rolling\ServiceInterface`.
- Updated canonical infrastructure/service consumers to use the canonical obligation-store interface.
- Added BC aliases for legacy and role-scoped obligation-store interface names.

## Why it matters
- Newer alias waves are now loaded automatically without editing the bootstrap every time.
- Canonical code under `src/ServiceInterface/*` now uses the canonical `App\Rolling\ServiceInterface` root consistently.
- Compatibility for historical FQCNs is preserved through alias bootstrap.
