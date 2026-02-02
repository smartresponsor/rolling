# RC-D10 — Docs & UX polish

This drop unifies SDKs with a real `POST /v2/check` endpoint, adds OpenAPI, HTTP recipes, and developer docs.

## Run all smokes

```bash
# Assuming you merged D2..D10:
bash repo/tools/rc4_pre_total_smoke.sh  # runs D2..D9
bash repo/tools/rc_d10_smoke.sh         # new lint + CLI check
```

## Minimal /v2/check contract

- Request:

```json
{"tenant":"t1","subject":"user:1","relation":"viewer","resource":"doc:42","context":{},"obligations":{"mask":["context.ssn"]}}
```

- Response:

```json
{"allowed":true,"meta":{"consistency":"eventual","token":"<bytes>","evidence":{...},"audit":{"audit":"logged","ts":"..."}}}
```

Headers echoed: `X-Role-Consistency`, `X-Role-Consistency-Token`.

## Files

- `src/Http/Role/Api/CheckController.php` — server-side `/v2/check` (TupleReader evidence + Audit Logger).
- `docs/openapi/role_v2.yaml` — OpenAPI 3.1 covering the public endpoints.
- `docs/api.http` — curl/HTTPie examples.
- `ops/postman/role.postman_collection.json` — ready to import.
- `public/role/debug/check.html` — tiny UI to hit `/v2/check`.
- `docs/adr/0001-layer-first-isolation.md` — architecture rationale.
- `docs/UX/api-errors.md`, `CHANGELOG.md`, `SECURITY.md`, `CONTRIBUTING.md`.

