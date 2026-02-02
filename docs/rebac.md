# ReBAC adapters (SpiceDB/OpenFGA)

- Interfaces in `src/InfraInterface/Role/Rebac/`.
- Implementations in `src/Infra/Role/Rebac/`.
- Offline `NullRebacClient` for dry-run & parity.

## Dry run

```
php tools/rebac/dry_run.php
cat report/rebac_dry_run.json
```

## Parity check (vs in-memory PDP)

```
php tools/rebac/parity_check.php
cat report/rebac_parity.json
```
