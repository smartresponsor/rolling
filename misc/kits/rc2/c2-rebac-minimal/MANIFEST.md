# RC-C2 — ReBAC minimal store + check/write APIs

- Tuple model: `ns, obj_type, obj_id, relation, subj_type, subj_id, subj_rel?`
- Consistency token: monotonically increasing `rev` (integer).
- Store: PDO (sqlite/pgsql) + in-memory impl.
- Services: Writer, Checker (with simple recursion for `object#relation` subject references).
- Endpoints (Symfony skeleton): POST `/v2/rebac/tuple/write`, POST `/v2/rebac/check`.
