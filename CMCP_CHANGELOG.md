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
- `tools/qa/current-summary.php` aggregates current evidence but currently records only generation time and evidence status, not the source Git commit. This leaves an RC provenance gap.
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
