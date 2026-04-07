# Rolling/Role w26 baseline regression comparison

This wave extends the perf and bench regression perimeter with baseline-file comparison.

## Scope

- add JSON baseline loader
- compare current perf reports against known-good perf baseline files
- compare current bench reports against known-good bench baseline files
- keep threshold-based gating from w25
- add comparative gating on top of threshold gating

## Added runtime pieces

- `JsonReportLoader`
- `PerfRegressionComparator`
- `BenchRegressionComparator`

## Operational outcome

Regression commands now accept `--baseline` and will fail when:

- absolute thresholds from w25 are violated
- comparative regression budgets against a known-good baseline are violated

## Comparative budgets

Perf comparison budgets:

- `--max-duration-regression-pct`
- `--max-per-item-regression-pct`
- `--max-peak-regression-pct`
- `--max-throughput-drop-pct`

Bench comparison budgets:

- `--max-p95-regression-pct`
- `--max-p99-regression-pct`
- `--max-batch-per-item-regression-pct`

## Notes

This wave does not invent external storage orchestration. It only adds file-based comparative gating on top of the existing report model.
