# role-pre-rc2-c4-opa-adapter

- OPA/Rego adapter for PDP v2: `OpaPdpV2` with `OpaHttpClient` and `InputBuilder`.
- Example policy in `examples/rego/role_v2.rego` with `decision` rule.

Quick start:

```bash
opa run --server -a :8181 examples/rego/role_v2.rego
php tools/rc_c4_smoke.sh
```
