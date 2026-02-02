# RC-C5 — Consistency tokens & cache invalidation (role-pre-rc2-c5-consistency-tokens)

- Composite token (policy_rev + rebac_rev [+ subject_epoch?]).
- Decorator cache keyed by request key + composite token.
- Headers helper: ETag + X-Role-Consistency for HTTP responses.
