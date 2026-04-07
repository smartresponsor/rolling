# Rolling/Role w24 benchmark diagnostics

This wave extends the native Symfony Console benchmarking perimeter with richer diagnostics.

## Added
- `app:role:perf:report`
- `app:role:bench:report`
- `--trace` option for detailed trace payloads
- `--detailed` option for richer scenario/runtime details
- persistent JSON reports under `var/bench_stats/`

## Intent
Keep performance diagnostics inside the native console layer instead of ad-hoc shell scripts.
