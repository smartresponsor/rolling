# Current operator workflow

This runbook is the stable RC-readiness workflow for the Rolling Symfony bundle. It describes only repository-owned checks; host-application wiring and deployment remain outside this package boundary.

## Preconditions

- PHP satisfies the `^8.4` package constraint.
- Composer is available.
- `composer install` has produced `vendor/autoload.php`.
- The sibling path repositories declared in `composer.json` resolve without copying their responsibilities into Rolling.

## Execution order

Run the checks from the repository root. Direct PHP entrypoints are authoritative for local operator use; Composer aliases exist for package scripts, but generated `current-*` evidence must still be tied to the exact source tree being evaluated:

```text
php tools/qa/dependency-readiness.php
php tools/qa/recovery-audits.php
php tools/qa/readiness-smoke.php
php tools/qa/operator-preflight.php
php tools/qa/current-summary.php
```

The final command reads the generated `report/recovery/current-*.json` artifacts and writes:

- `report/recovery/current-summary.json`
- `report/recovery/current-summary.pretty.txt`

It also records the exact 40-character Git revision as `source_commit`. Resolution accepts a valid CI commit (`GITHUB_SHA` or `CI_COMMIT_SHA`) or local Git metadata, including detached HEAD, loose refs, packed refs, and gitfile worktrees. If no exact commit can be resolved, `status.source_revision_known` is `false`, `status.evidence_complete` is `false`, and the summary emits an RC blocker instead of presenting provenance as known.

## Verdict interpretation

An RC-ready repository-level result requires:

- an exact source commit is resolved and recorded in the summary;
- dependency readiness reports Composer and `vendor/autoload.php` as present;
- no missing required PHP extensions;
- zero broken autoload entries;
- zero forbidden or external production roots;
- zero non-`App\\Rolling` namespace drift in active roots;
- the package QA, PHPStan, PHPUnit, and host-application container smoke pass in their appropriate environments.

`current-summary.php` is an aggregator, not a substitute for the underlying gates. Missing artifacts remain visible as `unknown`; explicit failed prerequisites are emitted as blockers.

The summary is fail-closed for evidence completeness: the source revision must resolve, and every required `current-*` input must exist and contain valid JSON. Missing or malformed evidence is listed in `status.missing_artifacts` or `status.invalid_artifacts`; unresolved source provenance is exposed through `status.source_revision_known`. Any of these conditions sets `status.evidence_complete` to `false` and is emitted as an RC blocker.

## Responsibility boundary

Rolling owns role and authorization behavior, its Symfony bundle wiring, diagnostics, and readiness evidence. Objecting owns reusable object system-field packs. Cruding owns generic CRUD controller and route formation. Viewing, Interfacing, and Navigating retain their presentation, shell, and navigation responsibilities. Do not move those surfaces into this repository to make a local gate pass.

## Operator closeout

Archive or publish only generated evidence that corresponds to the current Git commit. Regenerate all `current-*` artifacts after dependency, source, configuration, or QA-tool changes before making an RC claim.
