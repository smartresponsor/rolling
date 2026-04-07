# Rolling/Role w27 — baseline manifest and CI comparison profiles

This wave introduces managed baseline governance for performance and benchmark comparisons.

## Added capabilities
- baseline manifest storage under `var/bench_stats/baseline_manifest.json`
- known-good baseline promotion commands for `perf` and `bench`
- profile catalog under `config/role/perf_profiles.json`
- profile-aware regression checks that can resolve baselines from the manifest
- CI-friendly smoke/standard/strict tiers without copying thresholds into every pipeline job

## New commands
- `app:role:profile:list`
- `app:role:perf:baseline:promote`
- `app:role:bench:baseline:promote`
- `app:role:perf:profile-check`
- `app:role:bench:profile-check`
