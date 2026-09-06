# CMCP execution journal

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
- Confirmed Cruding parity data exists for six Rolling resource definitions; five remain explicitly marked as legacy-controller-backed. EasyAdmin audit identifies seven transitional generic CRUD/admin artifacts whose target owner is Cruding.
- Because `Destructive operations: FORBIDDEN`, the semantic moves cannot be completed factually in this run: adding canonical duplicates while retaining obsolete source classes/routes would violate the one-current-model canon. No fake duplicate migration was created.

### Iteration 3 — verification and fix

- `composer validate --strict --check-lock`: PASS.
- `composer qa`: PASS. PHP lint covered 614 files; PHPStan PASS; PHPUnit groups PASS (26 + 28 + 22 + 2); host smoke PASS (2 tests, 7 assertions); configured surface/Symfony/HTTP/docblock/checkbox/Cruding/EasyAdmin/Objecting/SOLID audits completed.
- `composer cs:check`: PASS, 0 of 600 files fixable.
- Objecting adoption audit reports zero system-field migration candidates and two explicit business exception candidates only (`status`, `createdAt` on the ACL mutation execution event).

### Iteration 4 — debt closure and integration

- The current dependency contour is correct: Rolling declares Objecting, Cruding, Viewing, and Interfacing and uses sibling path repositories for local development.
- RC-blocking structural debt remains bounded and explicit: true DTO `Payload`/`Dto` paths plus transitional generic EasyAdmin CRUD controllers/routes. Removing obsolete paths is necessary for a factual canonical completion, but deletion is prohibited by the task capability envelope.
- No sibling repository was modified. No speculative feature/growth work was introduced.

### Iteration 5 — final acceptance and handoff

- Verified checkpoint is quality-green but structural canonization is not factually complete under the current `Destructive operations: FORBIDDEN` constraint.
- Safe work completed in this continuation: factual recovery baseline, full mandatory contract reconciliation, generated-tail Git hygiene, current-tree audits, and complete configured QA/style validation.
- Acceptance status: `CHECKPOINT_GREEN_WITH_BOUNDED_CANON_BLOCKER`. A future task that explicitly permits exact source-path removals can finish the DTO renames/moves and Cruding controller/route cleanup from this verified baseline without redoing commit `cfa3f270`.

