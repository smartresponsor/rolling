# Current operator workflow

This repository now uses stable current-state recovery artifacts instead of wave-specific recovery files.

## Primary commands

- `php tools/qa/operator-preflight.php`
- `php tools/qa/dependency-readiness.php`
- `php tools/qa/readiness-smoke.php`
- `php tools/qa/recovery-audits.php`
- `php tools/qa/current-summary.php`

## Primary current-state artifacts

- `report/recovery/current-operator-preflight.json`
- `report/recovery/current-dependency-readiness.json`
- `report/recovery/current-readiness-smoke.json`
- `report/recovery/current-recovery-audits.json`
- `report/recovery/current-autoload-audit.json`
- `report/recovery/current-canon-scan.json`
- `report/recovery/current-namespace-audit.json`
- `report/recovery/current-summary.json`

## Operator intent

Use the current-state artifacts as the live recovery/readiness surface. Historical wave artifacts are not part of the operator workflow.

## Human-readable summaries

Use the paired `.pretty.txt` artifacts when you want a dry operator-facing text view without opening JSON:

- `report/recovery/current-dependency-readiness.pretty.txt`
- `report/recovery/current-readiness-smoke.pretty.txt`
- `report/recovery/current-operator-preflight.pretty.txt`
- `report/recovery/current-summary.pretty.txt`

Bootstrap preflight artifacts:
- `report/recovery/current-bootstrap-preflight.json`
- `report/recovery/current-bootstrap-preflight.pretty.txt`
- `report/recovery/current-recovery-audits.pretty.txt`
