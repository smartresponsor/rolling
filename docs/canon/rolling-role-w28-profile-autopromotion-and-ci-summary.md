# Rolling/Role w28 — profile autopromotion and CI summary

This wave extends the benchmark baseline governance introduced in w27.

## Additions

- Generic profile promotion command for `perf` and `bench` reports.
- Multi-profile summary command backed by the baseline manifest and the profile catalog.
- Composer shortcuts for smoke/standard/strict promotion and profile summary generation.

## Result

The console layer now supports baseline governance not only at a single-report level but also across named CI tiers.
