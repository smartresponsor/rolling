# Schema v2 (draft)

Goals:

- Zanzibar-like schema with `namespace`, `relations`, optional `caveats`.
- Versioned registry with `create(version,schema)` and `activate(version)`.
- Migration flow: diff → dry-run → apply (activate).

Breaking change signal = relation removal; non-breaking = add/change.
