# PDP v3 — Batch + Async/Stream

- **BatchDecisionInterface** with implementaion **BatchDecision**.
- CLI demo: `php tools/pdp/batch_demo.php` → `report/pdp_batch_demo.json`.
- Streaming (NDJSON): `php tools/pdp/batch_stream.php < input.ndjson > output.ndjson`.
- Routes draft: see `config/routes/role_pdp.yaml` (`/pdp/batch`, `/pdp/stream`).

## Notes

- Deterministic rule set for demo purposes (admin/reader/writer/owner).
- No external deps; pure PHP for portability.
- Latency is measured per request.
