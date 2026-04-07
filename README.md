# Rolling / Role

Current-slice repository under canonical remediation.

This wave (`w01`) establishes a protocol-grade baseline for subsequent cumulative repairs:
- freeze the current-slice audit against the declared Symfony-oriented canon;
- record structural violations without speculative code motion;
- define the next cumulative repair sequence so later waves can reshape code predictably.

Primary audit artifact:
- `docs/canon/rolling-role-w01-protocol-audit.md`

Machine-readable inventory:
- `report/rolling-role-w01-inventory.json`
- `report/rolling-role-w01-delivery.yaml`

## Canonicalization waves

- `w01`: factual protocol audit and inventory baseline.
- `w02`: root bootstrap normalization (`composer.json`) and repeatable canon scan utility.

## Canon waves

- w01: protocol audit baseline and machine-readable inventory
- w02: root composer/bootstrap baseline and local canon scan
- w03: collapse legacy root production trees under `src/Legacy/`

## Canon wave w04

- moved forbidden `src/Domain/Role/` to `src/Legacy/Domain/Role/`
- moved forbidden `src/Acl/Role/Adapter/` to `src/Legacy/Acl/Role/Adapter/`
- added Composer continuity mappings for legacy namespaces


## Canon wave w05

- moved eleven leaf `src/*/Role/...` groups out of canonical placement into `src/Legacy/...`
- preserved old namespaces through targeted Composer PSR-4 continuity mappings
- upgraded `bin/canon-scan.php` to distinguish canonical-placement violations from legacy-held violations

## Wave w06
- Relocated heavy internal role trees from canonical placement into `src/Legacy/...`:
  - `src/Infrastructure/Role/`
  - `src/InfrastructureInterface/Role/`
  - `src/Service/Role/`
  - `src/ServiceInterface/Role/`
- Added Composer `autoload.classmap` continuity for relocated legacy trees to avoid mass namespace rewrites in this wave.


## W07

- Completed final internal collapse of remaining canonical `src/*/Role/...` groups into `src/Legacy/...`.
- Canonical placement under `src/` now contains no forbidden `Role`, `Domain`, `Port`, `Adapter`, or `Rolling` directories.


## W08

- Added namespace baseline tooling and `bin/namespace-audit.php`.
- Recorded machine-readable namespace continuity metrics in `report/rolling-role-w08-namespace-audit.json`.

## W09

- Normalized canonical `src/Exception/*.php` to `App\Exception`.
- Added explicit compatibility aliases for legacy `SmartResponsor\RoleSdk\V2\Exception\*` class names.
- Reduced non-legacy non-`App\...` namespace files in canonical placement to zero.

- w10: no-namespace governance baseline added; canonical audit scripts namespaced; test namespace normalized.


## Canon waves
- w11: cleaned remaining unexpected legacy no-namespace tail and added compatibility alias for global RoleResolver.

## w12
- repaired root Composer SDK PSR-4 path to an existing repository location
- replaced selected legacy classmap fallbacks with explicit PSR-4 continuity mappings
- added autoload continuity audit script and report

- w13: reduced heavy autoload classmap tail by converting three legacy groups to explicit PSR-4 continuity mappings.

- w14: replaced broad legacy Service and ServiceInterface classmap continuity with explicit PSR-4 mappings; retained one file-level classmap outlier for App\Legacy\Service\Role\Pipeline\Stage\DecisionPipeline.
- w15: eliminated the final Composer classmap outlier by splitting App\Legacy\Service\Role\Pipeline\Stage\Stage into its own PSR-4 file; classmap is now 0.

## w16
- test + CLI foundation added: PHPUnit / PHPStan / PHP-CS-Fixer config, fixture catalog, scenario runner, and minimal bin/console fixture commands.


## w17 scenario CLI

The test foundation now includes business-oriented scenario commands for propagation and elimination:

- `php bin/console app:role:propagation:preview propagation-chain`
- `php bin/console app:role:propagation:run propagation-chain`
- `php bin/console app:role:elimination:preview elimination-cascade`
- `php bin/console app:role:elimination:run elimination-cascade`

Composer shortcuts:

- `composer scenario:propagation`
- `composer scenario:elimination`

## W18

Added richer business fixtures and scenario coverage for partial propagation, multi-hop propagation, and revoke-after-propagation. Added CLI scenario listing and extra scenario shortcuts in Composer.


## w19
- Added explain/audit CLI diagnostics.
- Added multi-tenant isolation, relation override, and deny-by-revocation business fixtures.

## w20
- Replaced bespoke CLI dispatch with a Symfony Console Application builder.
- Registered fixture, scenario, explain, and audit operations as native Console commands.
- Added CommandTester-friendly console tests and a Composer shortcut for `bin/console list`.


## Wave log
- w21: introduced DI-ready console registry/factory and migrated the first operational bin/role-* flows to native Symfony commands.


## w22
- Added native Symfony Console parity commands for policy/admin/janitor operational flows.


## w23 benchmark commands

- `php bin/console app:role:batch:perf 1000 0 128`
- `php bin/console app:role:bench:run 20000 3000 200`
- `php bin/console app:role:perf:stats 1000 0 128`
- `php bin/console app:role:bench:stats 20000 3000 200`


## W24 benchmark diagnostics

Added trace/detailed benchmark diagnostics and persistent report commands:
- `app:role:perf:report`
- `app:role:bench:report`

Default report output targets live under `var/bench_stats/`.


## W25 perf regression gating

New CI-friendly commands were added for perf and benchmark threshold checks:

- `php bin/console app:role:perf:regression-check`
- `php bin/console app:role:bench:regression-check`
- `composer qa:perf-smoke`


## w26

- Added baseline-file comparison for perf and bench regression checks.
- Regression commands can now fail both on absolute thresholds and on comparative drift versus known-good JSON reports.


## w27 baseline governance
- baseline manifest management for perf/bench reports
- CI comparison profiles in `config/role/perf_profiles.json`
- profile-aware perf/bench checks and baseline promotion commands


## w28
- Added generic profile promotion and multi-profile manifest summary commands.
- Added Composer shortcuts for smoke/standard/strict baseline promotion and CI summary generation.
