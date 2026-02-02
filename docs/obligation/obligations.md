# G6: Obligations, masking, audit

This package introduces obligation runner with audit + masking/redaction hooks.

Obligations supported:

- `audit.*` — writes a line into `var/audit/audit_YYYY-MM-DD.jsonl`
- `mask.<field>:<mode>` — modes: `redact`, `last4`, `hash`, `remove`
- `redact.<field>` — shorthand for `mask.<field>:redact`

Run demo:

```
php tools/obligation/demo.php
# → report/obligation_demo.json
# and audit line in var/audit/audit_YYYY-MM-DD.jsonl
```

Notes:

- Singular class names, EN-only comments, single-hyphen naming.
