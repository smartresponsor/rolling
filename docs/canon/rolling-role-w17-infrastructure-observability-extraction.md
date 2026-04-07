# Rolling Role w17

- Extracted winning infrastructure and observability classes from Legacy into canonical App layers.
- Added canonical InfrastructureInterface contracts for audit, policy, rebac, tenant.
- Added canonical Infrastructure implementations for audit, approval, policy, rebac, tenant.
- Added canonical Infrastructure observability metrics/tracing classes.
- Kept Legacy as BC bridge through alias file `legacy_role_w17_aliases.php`.
