# Rolling / Role

Symfony-first Rollin workspace for the Rolling component recovery track.

## Current status

The live repository tree has been structurally recovered from the earlier mixed workspace / legacy state:
- `src/Legacy/**` has been removed from the working tree.
- active `src/`, `tests/`, and `bin/` code paths no longer import `App\Legacy\...`.
- forbidden root production trees such as `Http/`, `PolicyInterface/`, and `Service/` are no longer present.
- the component now tracks a single Symfony-first application/workspace layout rooted in `src/`, `config/`, `bin/`, `tests/`, `docs/`, `report/`, and `tools/`.

## Current operator-facing references

Use the stable current-state artifacts instead of older wave-specific snapshots:
- `report/recovery/current-autoload-audit.json`
- `report/recovery/current-canon-scan.json`
- `report/recovery/current-namespace-audit.json`
- `report/recovery/current-dependency-readiness.json`
- `report/recovery/current-readiness-smoke.json`
- `report/recovery/current-operator-preflight.json`
- `report/recovery/current-recovery-audits.json`
- `report/recovery/current-summary.json`
- `report/recovery/current-summary.pretty.txt`

The live operator workflow now centers on stable `current-*` artifacts and their paired `.pretty.txt` text summaries.

## Expected layout

- `src/` — canonical Symfony-first production code under `App\...`
- `config/` — Symfony configuration and bundle wiring
- `bin/` — operational entrypoints and repository audit utilities
- `tests/` — PHPUnit and support fixtures
- `docs/` — current operator and component documentation
- `report/` — recovery, delivery, and package artifacts
- `tools/` — local QA helpers
- `sdk/` — SDK materials only
- `ops/` — operational reference files

## Recovery and readiness commands

Current operator-facing commands:
- `php tools/qa/recovery-audits.php`
- `php tools/qa/dependency-readiness.php`
- `php tools/qa/readiness-smoke.php`
- `php tools/qa/operator-preflight.php`
- `php tools/qa/current-summary.php`

These regenerate stable `report/recovery/current-*.json` artifacts for the live operator workflow:
- autoload continuity
- canon scan
- namespace audit
- dependency/bootstrap readiness
- readiness smoke
- combined operator preflight

## Runtime preflight

Before running `bin/console` or serving `public/index.php`, install dependencies with `composer install`. The repository now performs a bootstrap preflight and stops with an explicit recovery message when `vendor/autoload.php` is missing.

Recommended preflight sequence:
- `php tools/qa/dependency-readiness.php`
- `php tools/qa/recovery-audits.php`
- `php tools/qa/readiness-smoke.php`
- `php tools/qa/operator-preflight.php`
- `php tools/qa/current-summary.php`
- `php bin/console`

A local runtime needs:
- PHP satisfying `composer.json` (`^8.4`)
- required PHP extensions from `composer.json`
- Composer available on `PATH`
- `composer install` completed so `vendor/autoload.php` exists

## Remaining RC blockers

This snapshot is past the structural-recovery phase and is now in readiness hardening.

Still required before an RC-style verdict:
- dependency install and container compilation in a Composer-enabled environment
- PHPStan run
- PHPUnit run
- console/container smoke in a full Symfony runtime environment

## Recovery runbook

See `docs/recovery/current-operator-workflow.md` for the stable current-state operator workflow.

Bootstrap preflight artifacts:
- `report/recovery/current-bootstrap-preflight.json`
- `report/recovery/current-bootstrap-preflight.pretty.txt`
- `report/recovery/current-recovery-audits.pretty.txt`


## Repository structure

- `src/` — Symfony-first component core
- `config/` — Symfony configuration
- `tests/` — test suite
- `docs/` — documentation
- `tools/` — QA and operator tooling
- `report/` — generated reports and package artifacts
- `ops/` — operational assets
- `examples/` — non-core usage examples
- `sdk/` — embedded SDK materials (`php/`, `js/`, `java/`)

