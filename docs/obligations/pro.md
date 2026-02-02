# Obligations Pro (masking/redaction/headers/purpose) — RC5 E8

- Store: `var/policy/<tenant>/<version>/obligations.json`
- Rule shape:

```json
{
  "rules": [
    {
      "match": { "relation": "viewer", "effect": "ALLOW" },      // effect ANY|ALLOW|DENY
      "when": [ { "equals": { "path": "attrs.region", "value": "EU" } } ],
      "actions": [
        { "type": "mask",    "path": "resource.ssn",    "with": "****-**-####" },
        { "type": "redact",  "path": "resource.secret" },
        { "type": "header",  "name": "X-Data-Purpose", "value": "audit" },
        { "type": "purpose", "tag": "read-only" }
      ]
    }
  ]
}
```

- Endpoints:
    - `POST /v2/obligations/apply` → supply `decision.allowed`, `attrs`, optional `resource` ⇒
      returns `view`, `headers`, `actions`.
    - `POST /v2/check/oblige` → does decision via PolicyEngine (if present) and applies obligations.

Security notes:

- Masking with pattern containing `#` keeps last N digits (N = count of `#`), replaces others.
- Redaction removes the field.
- Headers are returned to be added by gateway/controller.

Generated: 2025-10-27T17:59:53Z UTC
