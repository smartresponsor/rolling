# Rolling / Role — protocol audit (`w01`)

## Scope

This document is based **only** on the current uploaded slice and is intended to anchor cumulative follow-up waves.

## Canon checked

- singular Symfony-oriented application/component;
- one production code root under `src/` with `App\Rolling\ -> src/`;
- canonical layer roots may exist as `src/[Layer]/...` and `src/[Layer]Interface/...`;
- root dot-folders are allowed;
- forbidden patterns include `src/Role/...`, `src/RoleInterface/...`, `src/.../Role/...`, `src/.../Rolling/...`, `src/Domain/...`, `src/Port...`, `src/Adaptor...`, `src/Infra...`, `src/opr...` and port/adaptor/hexagonal skeletons.

## Fact snapshot

- PHP files scanned: **426**
- External production-like roots outside `src/`: **4**
- Forbidden / non-canonical directories under `src/`: **117**
- `src/*` namespace path mismatches: **109**
- PHP files without namespace declaration: **75**

## Structural verdict

The slice is **not canonical** for the declared protocol.

### External competing production roots

The following trees live outside the canonical `src/` production root:

- `Http/`
- `Policy/`
- `PolicyInterface/`
- `Service/`


These must not remain as parallel production roots in later waves.

### Forbidden or non-canonical trees under `src/`

The scan found repeated `.../Role/...` layering together with `Domain`, `Port`, and `Adapter` vocabulary. Representative examples:

- `src/Acl/Role`
- `src/Acl/Role/Adapter`
- `src/Attribute/Role`
- `src/Attribute/Role/Cache`
- `src/Attribute/Role/Provider`
- `src/Audit/Role`
- `src/Audit/Role/Export`
- `src/Cache/Role`
- `src/Consistency/Role`
- `src/Consistency/Role/Policy`
- `src/Consistency/Role/Rebac`
- `src/Domain`
- `src/Domain/Role`
- `src/Domain/Role/Model`
- `src/Domain/Role/Port`
- `src/Entity/Role`
- `src/Housekeeping/Role`
- `src/Housekeeping/Role/Archive`
- `src/Infrastructure/Role`
- `src/Infrastructure/Role/Admin`
- `src/Infrastructure/Role/Approval`
- `src/Infrastructure/Role/Audit`
- `src/Infrastructure/Role/Key`
- `src/Infrastructure/Role/Obligation`
- `src/Infrastructure/Role/Policy`
- `src/Infrastructure/Role/Policy/Masking`
- `src/Infrastructure/Role/Rebac`
- `src/Infrastructure/Role/Residency`
- `src/Infrastructure/Role/Security`
- `src/Infrastructure/Role/Tenant`


The full inventory is stored in `report/rolling-role-w01-inventory.json`.

### Namespace drift

Representative mismatches between file path and declared namespace:

- `src/Entity/Role/PermissionKey.php` → declared `src\Entity\Role`, expected `App\Rolling\Entity\Role`
- `src/Entity/Role/Scope.php` → declared `src\Entity\Role`, expected `App\Rolling\Entity\Role`
- `src/Entity/Role/SubjectId.php` → declared `src\Entity\Role`, expected `App\Rolling\Entity\Role`
- `src/Exception/ApiException.php` → declared `SmartResponsor\RoleSdk\V2\Exception`, expected `App\Rolling\Exception`
- `src/Exception/BadRequestException.php` → declared `SmartResponsor\RoleSdk\V2\Exception`, expected `App\Rolling\Exception`
- `src/Exception/ForbiddenException.php` → declared `SmartResponsor\RoleSdk\V2\Exception`, expected `App\Rolling\Exception`
- `src/Exception/RateLimitException.php` → declared `SmartResponsor\RoleSdk\V2\Exception`, expected `App\Rolling\Exception`
- `src/Exception/RemoteErrorException.php` → declared `SmartResponsor\RoleSdk\V2\Exception`, expected `App\Rolling\Exception`
- `src/Exception/UnauthorizedException.php` → declared `SmartResponsor\RoleSdk\V2\Exception`, expected `App\Rolling\Exception`
- `src/Infrastructure/Cache/InMemoryCache.php` → declared `App\Rolling\Infra\Cache`, expected `App\Rolling\Infrastructure\Cache`
- `src/Infrastructure/Role/Admin/ApprovalFsStore.php` → declared `App\Rolling\Infra\Role\Admin`, expected `App\Rolling\Legacy\Infrastructure\Admin`
- `src/Infrastructure/Role/Admin/ApproverFsDirectory.php` → declared `App\Rolling\Infra\Role\Admin`, expected `App\Rolling\Legacy\Infrastructure\Admin`
- `src/Infrastructure/Role/Admin/InMemoryApprovalRequestRepository.php` → declared `App\Rolling\Infra\Role\Admin`, expected `App\Rolling\Legacy\Infrastructure\Admin`
- `src/Infrastructure/Role/Admin/OverrideFsPolicy.php` → declared `App\Rolling\Infra\Role\Admin`, expected `App\Rolling\Legacy\Infrastructure\Admin`
- `src/Infrastructure/Role/Approval/FileApprovalStore.php` → declared `App\Rolling\Infra\Role\Approval`, expected `App\Rolling\Legacy\Infrastructure\Approval`
- `src/Infrastructure/Role/Audit/FileAuditRepository.php` → declared `App\Rolling\Infra\Role\Audit`, expected `App\Rolling\Legacy\Infrastructure\Audit`
- `src/Infrastructure/Role/Audit/FileAuditTrail.php` → declared `App\Rolling\Infra\Role\Audit`, expected `App\Rolling\Legacy\Infrastructure\Audit`
- `src/Infrastructure/Role/Key/FileKeyProvider.php` → declared `App\Rolling\Infra\Role\Key`, expected `App\Rolling\Legacy\Infrastructure\Key`
- `src/Infrastructure/Role/Obligation/ObligationFsStore.php` → declared `App\Rolling\Infra\Role\Obligation`, expected `App\Rolling\Legacy\Infrastructure\Obligation`
- `src/Infrastructure/Role/Policy/InMemoryGrantRepository.php` → declared `App\Rolling\Infra\Role\Policy`, expected `App\Rolling\Legacy\Infrastructure\Policy`


### Files without namespace declaration

Representative entries:

- `bin/role-admin.php`
- `bin/role-batch-perf.php`
- `bin/role-bench.php`
- `bin/role-janitor.php`
- `bin/role-policy.php`
- `bin/role-rebac.php`
- `Http/Role/Role/Api/_note_consistency_dep.php`
- `Http/Role/Role/SymfonyBundle/Resources/config/services.php`
- `misc/kits/rc1/A/kits/role-rc-a3-hmac/tools/hmac_sign.php`
- `misc/kits/rc1/A/kits/role-rc-a5-sdk/examples/php/check.php`
- `misc/kits/rc1/B/kits/role-rc-b4-bench-profile/bench/Bootstrap.php`
- `misc/kits/rc1/B/kits/role-rc-b4-bench-profile/bench/PhpBench/ContextBench.php`
- `misc/repo/deploy/compose/kit_mount/tools/dev-router.php`
- `misc/repo/examples/watch_client.php`
- `misc/repo/examples/audit/emit.php`
- `misc/repo/examples/audit/read.php`
- `misc/repo/tools/audit_dump.php`
- `misc/repo/tools/backup_tenant.php`
- `misc/repo/tools/check_cli.php`
- `misc/repo/tools/e1_smoke.php`


## Wave policy

`w01` intentionally avoids broad code relocation. It is a **groundwork wave** that freezes the audit baseline and delivery protocol so later cumulative waves can reshape code without stale assumptions.

## Planned next waves

### `w02`
- remove competing production roots by preparing canonical destination layout under `src/`;
- start collapsing forbidden `Domain` / `Port` / `Adapter` vocabulary;
- keep logic movement minimal and traceable.

### `w03`
- begin de-featureizing `.../Role/...` directory insertions inside layers;
- align namespaces toward `App\Rolling\...` only where path ownership becomes canonical.

### `w04`
- finish namespace convergence;
- stabilize interface roots and service wiring;
- prepare a cleaner cumulative slice for deeper functional repair.
