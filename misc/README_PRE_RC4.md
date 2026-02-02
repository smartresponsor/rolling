# role-pre-rc4-d2..d10-total

Aggregates RC4 D-series envelopes D2..D10 into a single repo tree.

Included areas:

- D2: Schema v2 + migrations (HTTP /v2/model/*)
- D3: Consistency modes + SSE watch + cache invalidations
- D4: Admin plane (JWKS, rotate) + signing
- D5: Audit JSONL + obligations (mask/redact)
- D6: Integrations (Envoy ext_authz adapter, Oathkeeper example, Symfony bundle + Client)
- D7: SDKs (TS/Go/Java) with HMAC signing + retries
- D8: Explain/Plan + Debug UI (/v2/check/explain, DOT)
- D9: Multi-tenant ops (quota, limits, backup/restore)
- D10: Docs & UX polish + unified `/v2/check` + OpenAPI & Postman

Quick start:

```
unzip role-pre-rc4-d2..d10-total.zip
cd repo
bash tools/rc4_pre_total_smoke.sh
```
