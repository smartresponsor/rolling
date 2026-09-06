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

