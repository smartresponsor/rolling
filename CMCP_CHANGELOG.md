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
