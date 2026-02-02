# RC Gate (G10)

What:

- Smoke gate: `php -l` over configured paths.
- SLO gate: evaluate `report/metrics.json` against `config/role/rc_gate.json.slo` limits.
- Manifest + SHA256SUMS: inventory and checksums for all repo files (excluding report/).

Run:

```
php tools/rc/sample_metrics.php        # optional: write permissive metrics
php tools/rc/rc_gate.php               # → report/rc_gate.json, release/SHA256SUMS
cat report/rc_gate.json | jq .
```

Pass criteria:

- smoke.errors == 0
- slo.pass == true
- SHA256SUMS created
