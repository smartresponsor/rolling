# Current operator workflow

This runbook is the stable RC-readiness workflow for the Rolling Symfony bundle. It describes only repository-owned checks; host-application wiring and deployment remain outside this package boundary.

## Preconditions

- PHP satisfies the `^8.4` package constraint.
- Composer is available.
- `composer install` has produced `vendor/autoload.php`.
- The sibling path repositories declared in `composer.json` resolve without copying their responsibilities into Rolling.

## Execution order

Run the checks from the repository root:

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

## Verdict interpretation

An RC-ready repository-level result requires:

- dependency readiness reports Composer and `vendor/autoload.php` as present;
- no missing required PHP extensions;
- zero broken autoload entries;
- zero forbidden or external production roots;
- zero non-`App\\Rolling` namespace drift in active roots;
- the package QA, PHPStan, PHPUnit, and host-application container smoke pass in their appropriate environments.

`current-summary.php` is an aggregator, not a substitute for the underlying gates. Missing artifacts remain visible as `unknown`; explicit failed prerequisites are emitted as blockers.

The summary is fail-closed for evidence completeness: every required `current-*` input must exist and contain valid JSON. Missing or malformed evidence is listed in `status.missing_artifacts` or `status.invalid_artifacts`, sets `status.evidence_complete` to `false`, and is emitted as an RC blocker.

## Responsibility boundary

Rolling owns role and authorization behavior, its Symfony bundle wiring, diagnostics, and readiness evidence. Objecting owns reusable object system-field packs. Cruding owns generic CRUD controller and route formation. Viewing, Interfacing, and Navigating retain their presentation, shell, and navigation responsibilities. Do not move those surfaces into this repository to make a local gate pass.

## Operator closeout

Archive or publish only generated evidence that corresponds to the current Git commit. Regenerate all `current-*` artifacts after dependency, source, configuration, or QA-tool changes before making an RC claim.
