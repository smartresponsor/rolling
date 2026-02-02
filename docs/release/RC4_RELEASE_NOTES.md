# SmartResponsor/Role — RC4 Release Notes

Version: 0.4.0-rc4
Date (UTC): 2025-10-27T06:41:35.280988Z

## Scope

Feature-complete baseline for Role:

- Schema v2 & migrations; Consistency + SSE watch + cache invalidations
- Admin plane (JWKS, rotate), signing; Audit JSONL + obligations
- Integrations: Envoy ext_authz, Oathkeeper example, Symfony bundle + PHP client
- SDKs: TS / Go / Java (HMAC, retries, consistency)
- Explain/Plan endpoint + debug UI
- Multi-tenant ops: quotas, limits, backup/restore
- Docs & UX: unified /v2/check, OpenAPI 3.0, Postman, recipes

## Breaking changes

- API paths consolidated under `/v2/*`. Ensure SDKs use `/v2/check`.

## Known limitations

- Storage: dev-grade NDJSON for tuples (no external DB). Not HA.
- Strong consistency is stub-level for dev mode.
- Perf targets are not locked; see add-ons below.

## Add‑ons (post‑RC4 tracks, renamed from D11–D12)

- **rc4-addon-perf-baseline**: perf SLOs, cache layer metrics, batch checks, bloom, backpressure, tracing.
- **rc4-addon-security-hmac**: HMAC window, anti‑replay, nonce cache.
- **rc4-addon-resilience-chaos**: fault injection, durability & SSE resume.
- **rc4-addon-tenant-isolation**: isolation tests, policy guards.
- **rc4-addon-release-gate**: SLO summary, final gate report.

See `docs/release/rc4_addons_map.md` for mapping.
