# Rolling/Role w23 — benchmarking and stats console parity

This wave completes the remaining native Symfony Console migration for the legacy operational tails:

- `bin/role-batch-perf.php`
- `bin/role-bench.php`

## Added native commands

- `app:role:batch:perf`
- `app:role:bench:run`
- `app:role:perf:stats`
- `app:role:bench:stats`

## Design notes

- performance execution is isolated in dedicated runtimes;
- summary calculation is isolated in dedicated stats services;
- command registration stays DI-ready through the existing registry/factory layer.
