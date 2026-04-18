# Rolling wave 26 recovery

## Scope
- remove dead Symfony integration controller resource from `config/services.yaml`
- switch remaining `DecisionWithObligations` call sites from direct property access to canonical accessors
- restore canonical ReBAC tuple model expected by store/checker/tests

## Verified in working slice
- `php bin/console about` no longer fails on missing integration controller directory; it now reaches bootstrap preflight
- `phpstan analyse src tests` reports 0 errors
- local container phpunit cannot proceed because this environment lacks required PHP extensions (`dom`, `mbstring`, `xml`, `xmlwriter`)
