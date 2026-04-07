# Rolling/Role w25 — perf regression gating

This wave adds CI-friendly threshold checks around the benchmark/perf perimeter introduced in w23-w24.

Added commands:
- `app:role:perf:regression-check`
- `app:role:bench:regression-check`

Added capabilities:
- threshold evaluation for throughput, duration, per-item latency and memory peak
- threshold evaluation for p95/p99 scenario latency and batch-per-item latency
- non-zero exit code on violated thresholds for CI gating
- optional JSON report persistence through `--output`
