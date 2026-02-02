# RC-C4 — OPA/Rego adapter (role-pre-rc2-c4-opa-adapter)

- OpaPdpV2 — adapter implementing PdpV2Interface via OPA REST.
- InputBuilder — maps Role check params into OPA `input`.
- OpaHttpClient — minimal HTTP client (no external deps).
- Example Rego policy `examples/rego/role_v2.rego` with `decision` rule.
