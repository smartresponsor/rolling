# CMCP orchestration journal

Task: `engine-20260911155012-rolling-db1df5`
Component: Rolling
Execution mode: `AUTONOMOUS_REPOSITORY_RC`

## Iteration 1 — reconnaissance and baseline

Baseline HEAD: `5e71b60ae99adff118753e2747f1335f2ad57f46` (`master`).

Read and inspected:
- Rolling `AGENTS.md`, `README.md`, `composer.json`, recovery/operator documentation, QA scripts, tests/search surface, and current Git history.
- Objecting responsibility and Composer contract.
- Cruding responsibility, Composer contract, and agent rules.
- Viewing package/rendering boundary and Composer contract.
- Interfacing package/shell boundary and Composer contract.
- Gating executable-policy boundary and agent rules.
- Canonization repository contract, `MANIFEST.json`, canonical rule catalog, `Canon007Psr4IdentityRule`, and `Canon008ComposerDependencyIntegrityRule`.

Canon mapping for this workstream:
- Canon007: new PHPUnit types must retain literal path/namespace/type identity under `App\\Rolling\\Tests\\`.
- Canon008: this workstream adds no foreign runtime namespace usage and therefore requires no new Composer dependency.
- Objecting/Cruding/Viewing/Interfacing boundaries are non-applicable to the implementation itself because the selected change is repository-owned RC evidence tooling only; no entity, CRUD, rendering, shell, route, or UI responsibility moves into Rolling.

Current repository state:
- Rolling is a Symfony bundle package for role/authorization behavior.
- Repository documentation requires generated `current-*` RC evidence to correspond to the exact Git commit used for an RC verdict.
- `tools/qa/current-summary.php` aggregates current evidence but at baseline recorded only generation time and evidence status, not the source Git commit. This left an RC provenance gap.
- `CMCP_CHANGELOG.md` was absent at baseline.

Selected RC-critical work:
1. Add deterministic source-revision resolution for repository QA tooling.
2. Bind `current-summary.json` and its pretty report to an exact source commit.
3. Fail closed for RC evidence when the source commit cannot be resolved.
4. Add focused PHPUnit coverage and update the operator runbook.

Growth workstream (post-RC, not part of this task): richer policy simulation, explainability, impact analysis, delegated administration, policy history, and distributed decision-service integration as already separated by Rolling's RC maturity track.

Material risks:
- Worktree `.git` may be a directory or a gitfile (worktree/submodule style); resolver must handle both.
- CI environments may provide a detached checkout; explicit CI SHA variables should be accepted when valid.
- Evidence must never silently claim provenance when no exact 40-character commit can be established.

Planned gates:
- PHP syntax/lint for touched PHP files.
- Focused PHPUnit consistency test.
- Existing Rolling lint/PHPStan/test/QA and host-smoke where the execution environment provides dependencies.
- GitHub status/workflow checks for the integration commit.

### Что имеем?
A bounded, documented RC correctness gap in repository-owned evidence provenance with no need to cross component responsibility boundaries.

### Что осталось?
Implement the resolver and fail-closed summary contract, verify it, close documentation/journal tails, and integrate only after available gates are green.

## Iteration 2 — material implementation

Implemented on `cmcp/engine-20260911155012-rolling-db1df5`:
- added `tools/qa/source-revision.php` with exact 40-character SHA resolution from `GITHUB_SHA`, `CI_COMMIT_SHA`, detached HEAD, loose refs, packed refs, and gitfile worktrees;
- updated `tools/qa/current-summary.php` to emit `source_commit` and `status.source_revision_known`;
- made `status.evidence_complete` fail closed when source provenance is unresolved and added an explicit blocker;
- added `tests/Role/Consistency/SourceRevisionResolverTest.php` covering positive and negative resolution paths;
- updated `docs/recovery/current-operator-workflow.md` to make provenance part of the RC verdict contract.

No Composer dependency, production namespace, authorization behavior, Entity, Doctrine mapping, CRUD route/controller, rendering, or shell surface was changed.

### Что имеем?
The documented exact-commit requirement is now represented directly in generated RC summary evidence.

### Что осталось?
Execute available verification, inspect the resulting diff, and repair any in-scope failure.

## Iteration 3 — verification and fix

Verified in the available PHP `8.4.23` runtime:
- `php -l tools/qa/source-revision.php` — pass;
- `php -l tools/qa/current-summary.php` — pass;
- `php -l tests/Role/Consistency/SourceRevisionResolverTest.php` — pass;
- direct executable resolver checks — pass for CI SHA, detached HEAD, gitfile worktree, loose ref, packed ref, and unresolved metadata;
- `current-summary.php` positive provenance case — exact SHA recorded and provenance blocker absent;
- `current-summary.php` negative provenance case — `source_revision_known=false`, `evidence_complete=false`, provenance blocker present.

Verification limitation:
- Composer, PHPUnit, and PHPStan executables are not available in the execution container, so the full repository `composer qa`, PHPUnit, PHPStan, and host-smoke gates were not claimed as executed.

No verification defect requiring an implementation correction was found in the available runtime checks.

### Что имеем?
Touched PHP syntax and the complete resolver decision surface are verified in PHP 8.4, including fail-closed behavior.

### Что осталось?
Perform Git integration review, inspect available GitHub checks, and merge only if the repository merge gate is factually green.

## Iteration 4 — debt closure and integration

Integration facts:
- branch started from baseline `5e71b60ae99adff118753e2747f1335f2ad57f46` and remains zero commits behind `master` at review time;
- pull request #1 opened: `Harden Rolling RC evidence provenance`;
- GitHub reports the PR mergeable with no base conflict;
- diff is limited to five intended files: this journal, operator workflow, source-revision helper, current-summary integration, and focused consistency test;
- no generated evidence, vendor files, dependency lockfiles, or unrelated repository surfaces were changed.

GitHub gate facts:
- the repository has no pull-request QA workflow for PHP lint/PHPStan/PHPUnit/host smoke; `.github/workflows/` contains only sync automation workflows;
- the PR head therefore has no CI status to reinterpret as a green QA run;
- bounded runtime verification from iteration 3 remains the available implementation evidence.

### Что имеем?
A conflict-free, scope-bounded PR with explicit local verification evidence and no fabricated CI result.

### Что осталось?
Re-check PR head/base after this journal update, merge if still conflict-free, then inspect post-merge `master`/HEAD and close with iteration-5 acceptance facts.

---

# Prior recovery execution journal


## engine-20260906033244-rolling-70fdee

### Iteration 1 — reconnaissance and baseline

- Read the authoritative task specification, repository `README.md`, `composer.json`, current Git/worktree state, the PHP source inventory, and the Canonization architecture rules governing component prefixes, technical-role-first placement, interface mirroring, explicit DTO naming, and subject-folder depth.
- Read Objecting package responsibility and Composer contract; continue the remaining mandatory helper/read-and-comply contour before implementation.
- Baseline Git state: `master` at `5e71b60`, tracking `origin/master`, ahead/behind `0/0`; pre-existing untracked generated path `src/var/` is excluded from source canonization and must not be treated as authored PHP source.
- Concrete RC-critical work selected: reconcile Rolling's PHP tree/class names with Canonization and helper ownership, remove/rename only proven naming/tree anti-patterns, update all callers/config/tests/docs, then run configured Composer validation, lint, PHPStan, tests and repository audits.
- Material risks: broad legacy topology under `Infrastructure*`; early `Role` subject buckets; DTO-like `Payload`/`*Dto` names; generic `Contract/`; local CRUD controllers that may overlap Cruding ownership. Each requires semantic verification before mutation.
- Growth workstream (non-blocking): authorization DX/diagnostics and competitive maturity improvements are deferred unless a correctness/operability dependency is discovered.
- Gates planned: Composer validate; canon/namespace/autoload audits; lint; PHPStan; PHPUnit; host smoke where runnable; surface/objecting/cruding/docblock/solid audits; final Git cleanliness and upstream verification.

### Progress

- Iteration 1 baseline initialized.

### Iteration 2 — material implementation

- Migrated the Admin approval transport contract from `App\\Rolling\\Service\\Admin\\Dto\\AdminApprovalRequestDto` callers to canonical `App\\Rolling\\DTO\\Admin\\AdminApprovalRequestDTO` and added the canonical DTO source.
- Verified all changed PHP files with `php -l`; all are syntactically valid.
- Attempted the final source-path removal required to complete the semantic move, but the active Console write policy rejected source-file deletion (`File deletion is not allowed`). Because destructive operations are explicitly forbidden, no bypass was attempted.
- This runtime limitation also prevents safe completion of the remaining source-path renames/moves in this run; additive duplicate trees will not be manufactured merely to claim completion.

### Iteration 3 — verification and fix

- `composer phpstan`: PASS, no errors.
- `composer test`: PASS (26 + 28 + 22 + 2 tests).
- `composer host-smoke`: PASS (2 tests, 7 assertions).
- `composer cs:check`: initially reported two ordered-import findings introduced by the DTO migration; both were fixed and the re-run passed with 0 fixable files.
- Full `composer qa`: PASS after the fixes.

### Iteration 4 — debt closure and integration assessment

- `cruding-resource-readiness-audit`: ready, but reports 5 legacy-controller-backed resource definitions and explicitly lists removal of transitional EasyAdmin CRUD controllers after Cruding parity as the next step.
- `easyadmin-surface:audit`: reports 7 migration candidates and target state `zero generic CRUD controllers and zero generic CRUD routes in Rolling`.
- `objecting:adoption:audit`: no system-field candidates; two business exception candidates only.
- `docblock:coverage`: non-regressing but still baseline debt at 49.71% class and 41.2% public-method coverage; this is not 100% documentation maturity.
- Canon003 residuals remain visible in `src/DTO/Http/Role/**/*Payload.php` and additional `src/Service/**/Dto/*Dto.php` paths; completing their moves requires source-path removal, blocked by the active destructive-operation policy.
- Mandatory helper contour discrepancy remains: Rolling declares Objecting and Cruding, but its current Composer manifest does not declare Viewing or Interfacing as application dependencies.

### Iteration 5 — final acceptance and handoff

- Functional/static/style aggregate gate is green (`composer qa`).
- Naming/tree canonization is NOT factually complete because the authorized runtime forbids the source removals required for semantic moves and the repository still reports transitional CRUD ownership debt.
- Do not merge this work as a completed canonization claim. Preserve the verified bounded DTO consumer migration as a checkpoint only if Git integration is performed.

### Iteration 2 continuation — dependency contour and security hardening

- Added `interfacing/interface` and `viewing/view` as real Rolling Composer dependencies and added local path repositories with symlink wiring for development.
- Updated `composer.lock`; local `Interfacing` and `Viewing` are now installed via junctions, and compatible Symfony/Doctrine/local helper versions were refreshed.
- `composer audit` initially exposed CVE-2026-81892 in EasyAdmin; upgraded `easycorp/easyadmin-bundle` from 5.0.13 to 5.5.1 and re-ran the audit successfully with no advisories.
- Post-update verification: `composer validate --strict --check-lock` PASS; `composer qa` PASS before the security-only package bump; after the bump, the aggregate wrapper timed out, so deterministic sub-gates were re-run: PHPStan PASS, PHPUnit PASS (26 + 28 + 22 + 2), host smoke PASS (2 tests, 7 assertions), and prior `cs:check` remained clean before the package-only update.
- No sibling repository was modified; only Rolling manifest/lock/vendor installation state and existing Rolling task files were changed.

## engine-20260906053559-rolling-07b38a

### Iteration 1 — reconnaissance and baseline

- Recovery baseline re-inspected from the live worktree: `master` at `cfa3f270bc9490ae6c4bea4f74817aaabfdf2775`, tracking `origin/master`, ahead `1`, behind `0`; the only pre-existing/generated untracked tail is `src/var/` and remains excluded from authored-source canonization.
- Read the authoritative recovery specification, Rolling README/composer contract, Canonization AGENTS and Canon000/001/003/004/012 rules, plus Objecting and Cruding ownership/package contracts. Confirmed the required application dependency contour is declared in Rolling (`objecting/object`, `cruding/crud`, `viewing/view`, `interfacing/interface`) with local path repositories.
- Preserve the existing commit `cfa3f270` (`Canonize Rolling DTO and dependency contour`); do not revert, duplicate, or pretend to redo work already present.
- RC-critical workstream: run the repository's own canon/surface/DTO/Cruding/Objecting audits against the current tree, close only newly proven in-scope naming/tree and ownership debt, then execute lint/static/tests/style/Composer gates and Git integration.
- Growth workstream remains non-blocking: authorization DX/diagnostics and broader competitive maturity improvements are deferred unless required for correctness or operability.
- Material risks: generated `src/var/` must remain untouched; generic CRUD controller debt must be removed only when the current Cruding readiness contract proves parity; DTO classification must distinguish true transport DTOs from value objects/messages/view models.
- Planned gates: canon scan + namespace/autoload audits; HTTP payload, surface, Cruding readiness, EasyAdmin, Objecting, SOLID and docblock audits; `composer validate --strict --check-lock`; lint; PHPStan; PHPUnit; host smoke; CS check; final branch/upstream/worktree verification.

### Progress

- Iteration 1 baseline initialized from the live recovery state.

### Iteration 2 — material implementation

- Enumerated the tracked PHP surface from the live repository (614 PHP files linted by the configured gate; 638 PHP files inspected by the Symfony readiness audit including support surfaces) and reconciled naming/tree findings against Canon000/001/003/004.
- Added `/src/var/` to `.gitignore` because this exact path is the known pre-existing/generated runtime tail; generated output is now excluded from source-canon Git status without deleting or mutating generated contents.
- Re-ran Canon/HTTP/Cruding/Objecting audits. Confirmed remaining naming debt includes `src/DTO/Http/Role/**/*Payload.php` and multiple `src/Service/**/Dto/*Dto.php` classes. Canon003 requires real DTOs to move to `src/DTO/**` and use exact `DTO` suffix/casing.
- Iteration 2 continuation migrated the active Audit DTO contour to `App\\Rolling\\DTO\\Audit\\*DTO`: added four canonical DTO classes, retargeted `RuleExplainer`, `SimpleAuditLogger`, `ExplainerInterface`, and `AuditLoggerInterface`, and updated `docs/audit-explain-v2.md`. The obsolete `src/Service/Audit/Dto/*Dto.php` files remain only because source-path deletion is outside the current destructive-operation capability.
- Affected verification: changed-file PHP lint PASS after correcting one missing class brace caught immediately; PHPStan PASS; PHPUnit groups PASS (26 + 28 + 22 + 2); `cs:check` PASS after one ordered-import fix.
- Confirmed Cruding parity data exists for six Rolling resource definitions; five remain explicitly marked as legacy-controller-backed. EasyAdmin audit identifies seven transitional generic CRUD/admin artifacts whose target owner is Cruding.
- Because `Destructive operations: FORBIDDEN`, the semantic moves cannot be completed factually in this run: adding canonical duplicates while retaining obsolete source classes/routes would violate the one-current-model canon. No fake duplicate migration was created.

### Iteration 3 — verification and fix

- `composer validate --strict --check-lock`: PASS.
- `composer qa`: PASS. PHP lint covered 614 files; PHPStan PASS; PHPUnit groups PASS (26 + 28 + 22 + 2); host smoke PASS (2 tests, 7 assertions); configured surface/Symfony/HTTP/docblock/checkbox/Cruding/EasyAdmin/Objecting/SOLID audits completed.
- `composer cs:check`: PASS, 0 of 600 files fixable.
- Objecting adoption audit reports zero system-field migration candidates and two explicit business exception candidates only (`status`, `createdAt` on the ACL mutation execution event).
- Iteration 3 continuation verified the post-Audit residual Canon003 surface and migrated the active PDP DTO pair to `App\\Rolling\\DTO\\Pdp\\PdpDecisionRequestDTO` / `PdpDecisionResponseDTO`, retargeting `PdpBatchDecisionService` and `BatchDecisionInterface`. The obsolete `src/Service/Pdp/Dto/*Dto.php` sources remain only because exact source deletion is outside the current destructive-operation capability.
- Verification/fix result: changed-file PHP lint initially caught one missing closing brace in `PdpDecisionResponseDTO.php`; fixed immediately. Re-run lint PASS; PHPStan PASS; PHPUnit groups PASS (26 + 28 + 22 + 2); `cs:check` PASS with 0/606 fixable files; no active `App\\Rolling\\Service\\Pdp\\Dto` references remain under `src/`.

### Iteration 4 — debt closure and integration

- The current dependency contour is correct: Rolling declares Objecting, Cruding, Viewing, and Interfacing and uses sibling path repositories for local development.
- RC-blocking structural debt remains bounded and explicit: true DTO `Payload`/`Dto` paths plus transitional generic EasyAdmin CRUD controllers/routes. Removing obsolete paths is necessary for a factual canonical completion, but deletion is prohibited by the task capability envelope.
- No sibling repository was modified. No speculative feature/growth work was introduced.
- Iteration 4 continuation migrated the active permission catalog model to canonical `App\\Rolling\\DTO\\Permission\\PermissionDefinitionDTO`, retargeting `PermissionCatalog`, `PermissionCatalogConfigLoader`, `PermissionCatalogSnapshotService`, and `PermissionCatalogVersionHasher`. The obsolete `src/Service/Permission/Model/PermissionDefinitionDto.php` source remains only because exact source deletion is outside the current destructive-operation capability.
- Verification: changed-file PHP lint PASS; PHPStan PASS; PHPUnit groups PASS (26 + 28 + 22 + 2); `cs:check` PASS with 0/607 fixable files; no active `App\\Rolling\\Service\\Permission\\Model\\PermissionDefinitionDto` references remain under `src/`.
- Remaining Canon003 debt is now concentrated in the HTTP `*Payload.php` family and obsolete source-path tails from already-migrated DTO contours. Those tails cannot be factually removed in this run because destructive operations remain forbidden.

### Iteration 5 — final acceptance and handoff

- Verified checkpoint is quality-green but structural canonization is not factually complete under the current `Destructive operations: FORBIDDEN` constraint.
- Safe work completed in this continuation: factual recovery baseline, full mandatory contract reconciliation, generated-tail Git hygiene, current-tree audits, and complete configured QA/style validation.
- Acceptance status: `CHECKPOINT_GREEN_WITH_BOUNDED_CANON_BLOCKER`. A future task that explicitly permits exact source-path removals can finish the DTO renames/moves and Cruding controller/route cleanup from this verified baseline without redoing commit `cfa3f270`.
- Iteration 5 final acceptance re-ran the complete configured quality envelope. The first aggregate `composer qa` exposed a self-introduced docblock baseline regression (+1 undocumented class, +3 undocumented public methods) on the new canonical DTOs. Added only the missing DTO documentation; `docblock:coverage` then returned PASS with no regressions and improved coverage (classes 50.71%, public methods 41.67%). Full `composer qa` subsequently PASS; `composer cs:check` PASS; `composer validate --strict --check-lock` PASS; `composer audit` PASS with no advisories.
- Final bounded residual inventory: 8 obsolete `*Dto` source classes remain under legacy paths (Admin 1, Audit 4, PDP 2, Permission 1); 24 HTTP DTO classes remain named `*Payload` under `src/DTO/Http/Role/**`; Cruding readiness reports 6 resource definitions, 5 still marked legacy-controller-backed; EasyAdmin audit reports 7 migration candidates (6 admin controllers plus `config/routes/rolling_admin_easyadmin.yaml`) and 2 legacy findings. These require exact source/route removals and are not safely completable while destructive operations are forbidden.
- Final acceptance verdict for this task remains `CHECKPOINT_GREEN_WITH_BOUNDED_CANON_BLOCKER`: functional/static/style/security gates are green and the branch is integration-ready as a checkpoint, but naming/tree canonization is not factually complete.

