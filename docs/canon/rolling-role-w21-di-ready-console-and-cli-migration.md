# Rolling / Role — w21 DI-ready console and CLI migration start

## Scope

w21 moves the console layer one step closer to a Symfony-oriented application model:

- command registration is now service-based through a registry/factory pair;
- `RoleConsoleApplication` is a thin application builder over a command registry;
- the first operational `bin/role-*` scripts now have native console-command counterparts.

## What changed

### DI-ready console assembly

Added:

- `RoleCommandFactoryInterface`
- `RoleCommandRegistryInterface`
- `DefaultRoleCommandFactory`
- `DefaultRoleCommandRegistry`
- `RoleConsoleRuntime`

This keeps command creation out of `RoleConsoleApplication` and makes later container-backed registration possible without rewriting the application entrypoint.

### Operational command migration started

New native console commands:

- `app:role:rebac:write`
- `app:role:rebac:check`
- `app:role:policy:list`
- `app:role:admin:rebac:stats`
- `app:role:janitor:gc`

These are the first migration bridge from bespoke root scripts toward a canonical command layer.

## Boundary

w21 does **not** yet remove legacy root scripts. It starts migration and provides parity for a first operational subset.

Remaining root scripts still need follow-up migration waves, especially:

- `bin/role-policy.php` import/export/migrate flows
- `bin/role-admin.php` policy import/export/activate flows
- `bin/role-janitor.php` archive and replay variants
- perf / batch benches

## Operational examples

```bash
php bin/console app:role:rebac:check user:42 doc:1 viewer
php bin/console app:role:policy:list default-policy
php bin/console app:role:admin:rebac:stats
php bin/console app:role:janitor:gc --dsn=sqlite::memory:
```
