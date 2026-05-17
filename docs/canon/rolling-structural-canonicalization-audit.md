# Rolling structural canonicalization audit and milestone

Status: first cleanup wave based only on the supplied `Rolling(10).zip` current slice.

## Executive finding

Rolling already uses the component-scoped Symfony namespace `App\\Rolling\\` in `composer.json` and across `src/`. This should be preserved. The repository is not a plain `App\\` application and should not be migrated away from `App\\Rolling\\`.

The main problem is not the top namespace. The problem is mixed historical layering, inconsistent class naming, root-level SDK/runtime clutter, duplicated policy storage variants, and type files that do not always match PSR-4 autoload shape.

## Current factual baseline

- `composer.json` maps `App\\Rolling\\` to `src/`.
- `src/` contains 359 PHP files.
- All scanned `src/` PHP files declare an `App\\Rolling\\...` namespace.
- Top-level source distribution:
  - `Infrastructure`: 130 files.
  - `Service`: 92 files.
  - `ServiceInterface`: 42 files.
  - `Controller`: 22 files.
  - `Policy`: 19 files.
  - `InfrastructureInterface`: 18 files.
  - `Security`: 12 files.
  - `Integration`: 9 files.
  - `Exception`: 6 files.
  - `Net`: 4 files.
  - `Entity`: 3 files.
  - `Command`: 1 file.
- Root contains mixed operational and SDK artifacts: `client.ts`, `index.ts`, `index.ts.example`, `package.json`, `package-lock.json`, `playwright.config.js`, `compose.yaml`, `phpunit.xsd`, legacy patch notes, and QA configs.
- `deploy/docker/` already exists, but root `compose.yaml` still exposes deployment/runtime concerns at the root.
- Tests are split between `Tests\\...` and `App\\Rolling\\Tests\\...` autoload roots. This may be intentional during transition but should be normalized by milestone.

## High-confidence structural defects

### 1. PSR-4 namespace/path defects

Detected defects before this patch:

- `src/Infrastructure/Role/Policy/PolicyFsStore.php` declared `App\\Rolling\\Infra\\Role\\Policy`, while its path requires `App\\Rolling\\Infrastructure\\Role\\Policy`.
- `src/Security/Hmac/Verifier.php` contained multiple top-level autoload-critical symbols in one file: `SecretProviderInterface`, `InMemorySecretProvider`, `NonceStoreInterface`, `InMemoryNonceStore`, and `Verifier`.

This wave fixes only those low-risk autoload defects.

### 2. Naming convention drift

Most classes do not carry a repository/capability prefix. Examples include `AdminController`, `CheckController`, `PolicyController`, `AuditWriter`, `JsonAclSource`, `PdoAclSource`, `PolicyFsStore`, and many service classes.

Canonical direction for Rolling should be:

- externally visible and root-level capability classes should use `Rolling` or `RollingRole` prefix where ambiguity exists;
- Symfony type suffixes must remain explicit: `Controller`, `Command`, `Subscriber`, `Listener`, `Repository`, `Factory`, `Provider`, `Resolver`, `Store`, `Writer`, `Verifier`, `Guard`, `Processor`, `Catalog`, `Registry`, `Snapshot`, `Report`, `Result`, `Request`, `Response`, `Dto`;
- interfaces stay mirrored in `ServiceInterface` / `InfrastructureInterface` only when they mirror actual service/infrastructure directions;
- class names should describe the type, not only the business concept.

### 3. Layering drift

The repository contains parallel concepts under `Policy`, `Service/Policy`, `Infrastructure/Policy`, `Infrastructure/Role/Policy`, and `ServiceInterface/Policy`. This is the main cleanup axis.

Canonical direction:

- `Controller`: HTTP boundary only.
- `Command`: Symfony console boundary only.
- `Service`: business/application service logic.
- `ServiceInterface`: business service contracts.
- `Infrastructure`: filesystem/PDO/HTTP/vendor adapters and technical persistence.
- `InfrastructureInterface`: infrastructure contracts only.
- `Entity`: Doctrine/data entities only, entity-first and table-prefix audited.
- `Security`, `Net`, `Policy`, and `Integration` must be reviewed because they currently look like mixed concern buckets, not always type-identifiable Symfony layers.

### 4. Entity-first gap

Only three entity/value-object-like files are currently under `src/Entity/Role`: `PermissionKey`, `Scope`, `SubjectId`.

Rolling appears policy/RBAC/PDP-oriented, but the entity model is not yet the primary anchor. The milestone should decide which concepts are actual entities and which are value objects, DTOs, policy model objects, or service models.

Entity-first does not mean moving all code into Entity. It means the data model and table-prefix rules must be explicit before widening persistence or migrations.

### 5. Root cleanup gap

Root should retain project entry/config files only. SDK, generated clients, runtime docker/deploy files, old patch notes, reports, and miscellaneous operational assets should be relocated or explicitly documented as root exceptions.

Potential root actions for later waves:

- move root TypeScript client files into `SDK/js/` or remove if duplicated there;
- move `compose.yaml` under deploy ownership or keep a root shim only if required by local DX;
- move legacy `PATCH_NOTES.txt` and `DELETE_FILES.txt` into `docs/canon/legacy/` or remove by explicit touched-file deletion;
- review `misc/` and `report/` as generated/runtime outputs, not source.

## Milestone plan

### Wave 1 — audit baseline and autoload safety

Scope:

- add this audit document;
- add a repeatable structure audit helper;
- fix the two low-risk PSR-4/autoload defects described above;
- do not mass-rename classes yet.

Exit criteria:

- no `src/` file has a namespace/path mismatch under `App\\Rolling\\`;
- HMAC security helper symbols are autoloadable independently;
- future waves have a stable audit vocabulary.

### Wave 2 — root and deploy ownership

Scope:

- classify every root artifact as source, config, tool, deploy, SDK, generated, or legacy;
- move deploy-owned files under `deploy/` using touched-file moves only;
- move or retire duplicated root TypeScript SDK files;
- preserve root shims only when they are intentional developer-entry points.

Exit criteria:

- root contains only canonical project files;
- deploy/docker/local stack ownership is documented;
- no hidden duplicate SDK/client entry points remain.

### Wave 3 — controller and route naming canonicalization

Scope:

- rename controller classes to explicit Rolling/RollingRole names;
- fix `Consistency.php` to end with `Controller`;
- update route imports/references/tests in the same touched patch;
- keep controllers thin and route-only.

Exit criteria:

- every controller file/class ends with `Controller`;
- controller names are component-aware and route intent is explicit;
- no route points to old controller FQCNs.

### Wave 4 — service and interface shape normalization

Scope:

- align `Service` and `ServiceInterface` by direction;
- eliminate duplicate or misleading contracts;
- keep mirrored interfaces where they serve real seam/testing needs;
- move DTO/model classes out of service folders only where class type demands it.

Exit criteria:

- service directories represent business capability directions;
- service classes have clear type suffixes;
- interfaces have a one-to-one or intentionally documented relationship to implementations.

### Wave 5 — policy/RBAC/PDP consolidation

Scope:

- reconcile `Policy`, `Service/Policy`, `Infrastructure/Policy`, and `Infrastructure/Role/Policy`;
- select canonical homes for PEL, PDP, obligations, masking, policy registry, and policy stores;
- remove duplicate policy filesystem stores through touched-file deletions only after references are migrated.

Exit criteria:

- no duplicate policy store class with same conceptual responsibility remains;
- policy model vs policy service vs policy infrastructure boundaries are unambiguous.

### Wave 6 — entity-first model review

Scope:

- define canonical Rolling entity/table prefix;
- classify `PermissionKey`, `Scope`, `SubjectId` as entity/value object/model as appropriate;
- introduce missing entities only if they are required by business/persistence workflows;
- document table naming rules for Rolling.

Exit criteria:

- entity model is explicit;
- Doctrine/table prefix policy is documented;
- repository/storage classes align to the entity model.

### Wave 7 — tests and QA namespace normalization

Scope:

- normalize tests to one canonical test namespace strategy;
- align fixtures and smoke tests with moved classes;
- add structure-audit checks to QA without making them destructive.

Exit criteria:

- tests do not depend on stale class aliases;
- fixture files remain fixture files, not namespaced test classes;
- QA catches namespace/path and controller suffix drift.

### Wave 8 — compatibility tail removal

Scope:

- remove aliases/bridges introduced only for transition;
- delete stale files explicitly through touched-file manifests;
- update docs and release notes.

Exit criteria:

- no compatibility tail remains unless intentionally documented;
- cleanup is reproducible from touched-file patches only.

## First-wave touched-file note

This patch intentionally avoids broad renames. Rolling has a large class graph, and mass renaming without staged reference updates would create avoidable breakage. The safe first step is to stabilize audit and autoload shape, then perform class-family waves.

## Wave 2 applied note — controller helper extraction

The first structural follow-up after W01 removes the last known controller suffix defect without pretending the helper is a controller.

Applied direction:

- `src/Controller/Api/Consistency.php` is deleted as an incorrectly placed non-controller helper.
- `ConsistencyHeaderService` is introduced under `src/Service/Consistency/Http/` because the class resolves and applies HTTP consistency headers for an application service flow.
- `CheckController` now depends on the service helper statically to keep this wave minimal and avoid container wiring changes.

Verification after W02:

- `tools/qa/rolling-structure-audit.php` reports no PSR-4 defects.
- `tools/qa/rolling-structure-audit.php` reports no controller suffix defects.

The broader controller naming wave is still open: controller classes may later receive component-aware `Rolling...Controller` names, but this patch intentionally avoids mass route/reference churn.

## Wave 3 applied note — root TypeScript SDK ownership

W03 starts the root-cleanup milestone without touching the whole repository tree.

Applied direction:

- The canonical TypeScript SDK entry point is now `SDK/js/index.ts`.
- `SDK/js/package.json` now publishes `dist/index.js` and `dist/index.d.ts` instead of exposing `dist/client.*` as the package root.
- `SDK/js/README.md` now imports from `@smartresponsor/role-sdk` and no longer points at the obsolete `/v2` entry form.
- The old root-level TypeScript client files `client.ts`, `index.ts`, and `index.ts.example` are classified as duplicate root SDK entry points and are removed by the apply script with backup.
- `tools/qa/rolling-structure-audit.php` now reports and fails on those forbidden root SDK entry points if they reappear.
- `playwright.config.js` now points at the actual case-sensitive `tests/E2E` directory.

Verification target after W03:

- `php tools/qa/rolling-structure-audit.php` must report `root_sdk_entrypoint_defects: []`.
- root no longer contains duplicate SDK source files.
- TypeScript SDK ownership is under `SDK/js/`.

Still open after W03:

- `compose.yaml` remains at root as a developer entry point until a later deploy wave decides whether to move it under `deploy/` or keep it as an intentional root shim.
- root QA manifests such as `package.json`, `package-lock.json`, `playwright.config.js`, `phpunit.xml`, and static-analysis configs remain because they are project-level tooling entry points, not SDK source clutter.

## Wave 4 applied note — deploy ownership and root legacy note archive

W04 establishes a deploy-owned compose surface without removing the conventional root `docker compose` developer entrypoint.

Applied direction:

- `deploy/compose/compose.yaml` is the canonical compose file owned by the deploy surface.
- Root `compose.yaml` remains only as a developer-experience shim and is documented as non-canonical ownership.
- `deploy/README.md` documents deploy file ownership and the root shim policy.
- Loose root patch-note artifacts are archived into `docs/canon/legacy/rolling-legacy-root-patch-notes.md`.
- Root legacy notes are removed through touched-file deletion with backup in the apply script.
- `tools/qa/rolling-structure-audit.php` now detects forbidden root legacy notes and missing deploy-owned compose/docker files.

Verification after W04:

```bash
php tools/qa/rolling-structure-audit.php
```

Expected result: zero PSR-4 defects, zero controller suffix defects, zero root SDK entrypoint defects, zero root legacy note defects, and zero deploy ownership defects.

## Wave 5 applied note — consistency service class-form normalization

W05 starts class-form canonicalization with a narrow, mechanically verifiable service rename instead of a broad service-layer migration.

Applied direction:

- `src/Service/Consistency/Composer.php` is classified as a legacy generic service name because `Composer` does not expose the class responsibility at file/class level.
- `src/Service/Consistency/ConsistencyTokenComposer.php` is introduced as the canonical service name for composing consistency tokens.
- `BulkController` now depends on `ConsistencyTokenComposer` explicitly.
- The consistency cache test now uses `ConsistencyTokenComposer` explicitly.
- The old `Composer.php` file is removed through the W05 apply script with backup.
- `tools/qa/rolling-structure-audit.php` now detects the return of the legacy `src/Service/Consistency/Composer.php` file.

Verification after W05:

```bash
php tools/qa/rolling-structure-audit.php
```

Expected result: zero PSR-4 defects, zero controller suffix defects, zero root SDK entrypoint defects, zero legacy consistency service defects, zero root legacy note defects, and zero deploy ownership defects.

Still open after W05:

- The same class-form pass should continue across obvious generic service names such as short DTO/model names, cache helpers, and infrastructure acronyms.
- Token value objects under consistency should be handled as a separate wave because they affect multiple imports and namespace semantics.

## Wave 6 applied note — cache service class-form normalization

W06 continues class-form canonicalization inside the cache service area with a narrow, mechanically verifiable rename set.

Applied direction:

- `src/Service/Cache/Cache.php` is classified as a legacy generic service name because `Cache` does not expose Rolling-specific responsibility at file/class level.
- `src/Service/Cache/Invalidation.php` is classified as a legacy generic service name because `Invalidation` is too broad outside the cache context.
- `src/Service/Cache/Partitioner.php` is classified as a legacy generic service name because `Partitioner` does not identify tenant/cache sharding responsibility.
- `src/Service/Cache/RollingDecisionCache.php` is introduced as the canonical cache service for tenant/subject/relation/resource decision entries.
- `src/Service/Cache/RollingDecisionCacheInvalidator.php` is introduced as the canonical tuple-write invalidation service.
- `src/Service/Cache/TenantCacheShardPartitioner.php` is introduced as the canonical tenant shard partitioning helper.
- The old generic cache service files are removed through the W06 apply script with backup.
- `tools/qa/rolling-structure-audit.php` now detects the return of the legacy cache service filenames.

Verification after W06:

```bash
php tools/qa/rolling-structure-audit.php
```

Expected result: zero PSR-4 defects, zero controller suffix defects, zero root SDK entrypoint defects, zero legacy consistency service defects, zero legacy cache service defects, zero root legacy note defects, and zero deploy ownership defects.

Still open after W06:

- Continue class-form cleanup for other short generic service names such as `Diff`, `Validation`, `Checker`, `Writer`, `Backup`, `Restore`, `Limits`, and `Quota`.
- Token value objects under consistency remain intentionally untouched in this wave because they require namespace/import review beyond mechanical filename replacement.

## Wave 7 applied note — audit service class-form normalization

W07 continues class-form canonicalization inside the audit service area with a narrow, mechanically verifiable rename set.

Applied direction:

- `src/Service/Audit/Logger.php` is classified as a legacy generic service name because `Logger` does not expose that the class writes Rolling decision audit records.
- `src/Service/Audit/Redactor.php` is classified as a legacy generic service name because `Redactor` does not expose that the class redacts decision audit payloads according to obligations.
- `src/Service/Audit/DecisionAuditLogWriter.php` is introduced as the canonical service name for JSONL decision audit writes.
- `src/Service/Audit/DecisionAuditPayloadRedactor.php` is introduced as the canonical service name for masking/redaction of decision audit payloads.
- `CheckController` now depends on `DecisionAuditLogWriter` explicitly.
- The old generic audit service files are removed through the W07 apply script with backup.
- `tools/qa/rolling-structure-audit.php` now detects the return of the legacy audit service filenames.

Verification after W07:

```bash
php tools/qa/rolling-structure-audit.php
```

Expected result: zero PSR-4 defects, zero controller suffix defects, zero root SDK entrypoint defects, zero legacy consistency/cache/audit service defects, zero root legacy note defects, and zero deploy ownership defects.

Still open after W07:

- Continue class-form cleanup for short generic service names such as `Diff`, `Validation`, `Checker`, `Writer`, `Backup`, `Restore`, `Limits`, and `Quota`.
- Token value objects under consistency remain intentionally untouched because they require namespace/import review beyond mechanical filename replacement.


### W08 — ReBAC service class-form cleanup

Status: prepared as touched-files patch.

Canonicalized generic ReBAC service names:

- `src/Service/Rebac/Checker.php` -> `src/Service/Rebac/RebacRelationshipChecker.php`
- `src/Service/Rebac/Writer.php` -> `src/Service/Rebac/RebacRelationshipWriter.php`

The old files are removed only by the apply script with backup. The service meaning stays unchanged; this wave only makes the class-form explicit for the Smart Responsor naming convention.


## Wave 9 applied note — model service class-form normalization

W09 continues class-form canonicalization inside the model service area with a narrow, mechanically verifiable rename set.

Applied direction:

- `src/Service/Model/Diff.php` is classified as a legacy generic service name because `Diff` does not expose that the class calculates model schema relation differences.
- `src/Service/Model/Validation.php` is classified as a legacy generic service name because `Validation` does not expose that the class validates Rolling model schema payloads.
- `src/Service/Model/ModelSchemaDiffCalculator.php` is introduced as the canonical model schema diff service.
- `src/Service/Model/ModelSchemaValidator.php` is introduced as the canonical model schema validation service.
- `src/Service/Model/Migrator.php` now calls `ModelSchemaDiffCalculator` explicitly.
- `src/Service/Model/SchemaRegistry.php` now calls `ModelSchemaValidator` explicitly.
- The model diff test was renamed to `tests/Role/Model/ModelSchemaDiffCalculatorTest.php` so the test file/class form matches the tested service.
- The old generic model service files and old generic test file are removed through the W09 apply script with backup.
- `tools/qa/rolling-structure-audit.php` now detects the return of legacy model service filenames.

Verification after W09:

```bash
php tools/qa/rolling-structure-audit.php
composer dump-autoload
composer test
```

Expected result: zero PSR-4 defects, zero controller suffix defects, zero root SDK entrypoint defects, zero legacy consistency/cache/audit/ReBAC/model service defects, zero root legacy note defects, and zero deploy ownership defects.

Still open after W09:

- Continue class-form cleanup for remaining short generic names such as `Migrator`, `SchemaRegistry`, `Backup`, `Restore`, `Limits`, and `Quota` only where the target name can be verified mechanically.
- Entity-first restructuring remains a separate milestone and should not be mixed into naming-only service cleanup waves.

## W10 applied note — tenant service class-form normalization

W10 continues the mechanically verifiable class-form cleanup inside the tenant service area.

Applied direction:

- `src/Service/Tenant/Backup.php` is classified as a legacy generic service name because `Backup` does not identify that the class writes tenant backup archives.
- `src/Service/Tenant/Restore.php` is classified as a legacy generic service name because `Restore` does not identify that the class restores tenant backup archives.
- `src/Service/Tenant/Limits.php` is classified as a legacy generic service name because `Limits` does not identify the tenant limit configuration responsibility.
- `src/Service/Tenant/Quota.php` is classified as a legacy generic service name because `Quota` does not identify the tenant request quota responsibility.
- Canonical replacements are:
  - `src/Service/Tenant/TenantBackupArchiveWriter.php`
  - `src/Service/Tenant/TenantBackupArchiveRestorer.php`
  - `src/Service/Tenant/TenantLimitConfigurationService.php`
  - `src/Service/Tenant/TenantRequestQuotaService.php`
- `src/Controller/Api/Admin/TenantAdminController.php` now depends on explicit tenant service class forms.
- The old generic tenant service files are removed through the W10 apply script with backup.
- `tools/qa/rolling-structure-audit.php` now detects the return of legacy tenant service filenames.

Verification after W10:

```bash
php tools/qa/rolling-structure-audit.php
composer dump-autoload
composer test
```

Expected result: zero PSR-4 defects, zero controller suffix defects, zero root SDK entrypoint defects, zero legacy consistency/cache/audit/ReBAC/model/tenant service defects, zero root legacy note defects, and zero deploy ownership defects.

Still open after W10:

- Continue class-form cleanup for remaining generic service names only where references are mechanically traceable.
- `Model/Migrator` and `Model/SchemaRegistry` are candidates, but should be handled separately because their names may already be domain-recognizable and need a responsibility decision.
- Entity-first restructuring remains a separate milestone and should not be mixed into naming-only service cleanup waves.


## W11 applied note — model registry/migration service class-form normalization

W11 completes the mechanically safe model service class-form cleanup that W09 intentionally left open.

Applied direction:

- `src/Service/Model/Migrator.php` is classified as a legacy generic service name because `Migrator` does not identify that the class plans and applies Rolling model schema migrations.
- `src/Service/Model/SchemaRegistry.php` is classified as a legacy generic service name because `SchemaRegistry` does not identify that the registry is specifically for Rolling model schemas.
- Canonical replacements are:
  - `src/Service/Model/ModelSchemaMigrationService.php`
  - `src/Service/Model/ModelSchemaRegistry.php`
- `ModelSchemaMigrationService` depends on `ModelSchemaRegistry`, preserving the previous behavior while making the responsibility explicit.
- The old generic model service files are removed through the W11 apply script with backup.
- `tools/qa/rolling-structure-audit.php` now detects the return of `Migrator.php` and `SchemaRegistry.php` as legacy model service filenames.

Verification after W11:

```bash
php tools/qa/rolling-structure-audit.php
composer dump-autoload
composer test
```

Expected result: zero PSR-4 defects, zero controller suffix defects, zero root SDK entrypoint defects, zero legacy consistency/cache/audit/ReBAC/model/tenant service defects, zero root legacy note defects, and zero deploy ownership defects.

Still open after W11:

- Continue only small, traceable class-form cleanup waves for remaining generic names in `Service/Explain`, `Service/Pipeline`, `Service/Policy`, and `Service/Permission`.
- Entity-first restructuring remains a separate milestone and should not be mixed into naming-only service cleanup waves.

## W12 applied note — explain service class-form normalization

W12 continues small, mechanically traceable class-form cleanup inside `src/Service/Explain`.

Applied direction:

- `src/Service/Explain/TupleReader.php` is classified as a legacy generic service name because `TupleReader` does not identify the Rolling ReBAC tuple-log evidence responsibility.
- `src/Service/Explain/Planner.php` is classified as a legacy generic service name because `Planner` does not identify decision explanation planning responsibility.
- `src/Service/Explain/Renderer.php` is classified as a legacy generic service name because `Renderer` does not identify DOT rendering for decision explanations.
- Canonical replacements are:
  - `src/Service/Explain/RelationshipTupleLogReader.php`
  - `src/Service/Explain/DecisionExplanationPlanner.php`
  - `src/Service/Explain/DecisionExplanationDotRenderer.php`
- `src/Controller/Api/CheckController.php` now depends on the explicit tuple-log reader class form.
- Explain documentation now uses the explicit service names.
- The old generic explain service files are removed through the W12 apply script with backup.
- `tools/qa/rolling-structure-audit.php` now detects the return of the legacy explain service filenames.

Verification after W12:

```bash
php tools/qa/rolling-structure-audit.php
composer dump-autoload
composer test
```

Expected result: zero PSR-4 defects, zero controller suffix defects, zero root SDK entrypoint defects, zero legacy consistency/cache/audit/ReBAC/model/tenant/explain service defects, zero root legacy note defects, and zero deploy ownership defects.

Still open after W12:

- Continue only small, traceable class-form cleanup waves for remaining generic names in `Service/Pipeline`, `Service/Policy`, and `Service/Permission`.
- Entity-first restructuring remains a separate milestone and should not be mixed into naming-only service cleanup waves.

## W28 - Audit DTO class-form cleanup

Status: prepared.

Scope:
- `src/Service/Audit/Dto/DecisionInput.php` -> `AuditDecisionInputDto.php`
- `src/Service/Audit/Dto/DecisionResult.php` -> `AuditDecisionResultDto.php`
- `src/Service/Audit/Dto/DecisionRecord.php` -> `AuditDecisionRecordDto.php`
- `src/Service/Audit/Dto/ExplainNode.php` -> `AuditExplainNodeDto.php`

Rationale: audit DTO classes now expose explicit DTO-form names and no longer use short generic names in the canonical service surface.

### W30 — PDP policy tuple mapper class-form cleanup

W30 removes the remaining short PDP policy mapper class name from the canonical service surface.

- Retired touched legacy file: `src/Service/Pdp/Policy/TupleMapper.php`.
- Added canonical service-form class: `src/Service/Pdp/Policy/PdpPolicyTupleMapper.php`.
- Added audit key: `legacy_pdp_policy_service_defects`.

This keeps PDP policy mapping explicit and avoids a generic `TupleMapper` class name in a component-wide service tree.

### W31 - Cache support class-form cleanup

W31 moves the remaining short cache support service names to explicit service-form names:

- `src/Service/Cache/StampedeGuard.php` -> `src/Service/Cache/CacheStampedeGuardService.php`;
- `src/Service/Cache/SubjectEpochs.php` -> `src/Service/Cache/SubjectCacheEpochRegistry.php`;
- `src/Service/Cache/TagInvalidator.php` -> `src/Service/Cache/FileBackedCacheTagInvalidator.php`.

The cache invalidator interface is intentionally preserved as the stable contract. The structural audit now rejects the legacy cache support filenames.

### W32 - Mask service class-form cleanup

W32 moves the remaining short mask service implementation name to an explicit service-form name:

- `src/Service/Mask/DataMasker.php` -> `src/Service/Mask/ObligationDataMaskingService.php`.

The stable `DataMaskerInterface` contract is intentionally preserved to keep existing consumers and autowiring contracts narrow. The structural audit now rejects the legacy mask service filename through `legacy_mask_service_defects`.

### W33 - Obligation service class-form cleanup

W33 moves remaining generic obligation implementation names to explicit service-form names:

- `src/Service/Obligation/BasicObligationRunner.php` -> `src/Service/Obligation/ObligationEffectRunnerService.php`;
- `src/Service/Obligation/ObligationApplier.php` -> `src/Service/Obligation/PolicyObligationApplierService.php`.

The structural audit rejects the legacy obligation filenames through `legacy_obligation_service_defects`.

### W34 - PDP batch service class-form cleanup

W34 moves the remaining short PDP batch implementation name to an explicit service-form name:

- `src/Service/Pdp/BatchDecision.php` -> `src/Service/Pdp/PdpBatchDecisionService.php`.

The stable `BatchDecisionInterface` contract is intentionally preserved to avoid widening this wave. The structural audit rejects the legacy PDP batch implementation filename through `legacy_pdp_batch_service_defects`.

### W35 - Residency guard service class-form cleanup

W35 moves the remaining short residency implementation name to an explicit service-form name:

- `src/Service/Residency/ResidencyGuard.php` -> `src/Service/Residency/TenantDataResidencyGuardService.php`.

The stable `ResidencyPolicyInterface` contract is intentionally preserved. `ResidencyController` now depends on the explicit tenant data residency guard service. The structural audit rejects the legacy residency guard implementation filename through `legacy_residency_service_defects`.

## W36 — Infrastructure HTTP client class-form cleanup

- Retired legacy generic infrastructure HTTP filename `src/Infrastructure/Http/Client.php`.
- Added canonical class-form implementation `src/Infrastructure/Http/RollingDecisionHttpClient.php`.
- Extended `tools/qa/rolling-structure-audit.php` with `legacy_infrastructure_http_service_defects` so the generic infrastructure client name cannot silently return.

## W37 — ReBAC infrastructure HTTP client class-form cleanup

- Retired legacy generic ReBAC infrastructure filename `src/Infrastructure/Rebac/HttpClient.php`.
- Added canonical class-form implementation `src/Infrastructure/Rebac/RebacRelationshipHttpJsonClient.php`.
- Extended `tools/qa/rolling-structure-audit.php` with `legacy_rebac_http_infrastructure_service_defects` so the generic ReBAC HTTP client name cannot silently return.
