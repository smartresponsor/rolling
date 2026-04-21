# Schema v2 (draft)

Goals:

- Zanzibar-like schema with `namespace`, `relations`, optional `caveats`.
- Versioned registry semantics remain part of the internal/package model layer.
- Host applications own any mutation HTTP surface for schema lifecycle (create/activate/apply).

Breaking change signal = relation removal; non-breaking = add/change.
