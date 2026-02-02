# Explain/Plan + Debug UI (RC-D8)

Endpoint:

- `POST /v2/check/explain[?dot=1]`
    - Body: `{ "tenant":"t1","subject":"user:1","relation":"viewer","resource":"doc:42" }`
    - Returns JSON with `allowed`, `token` (consistency watermark = bytes of tuples
      log), `explain: {nodes, edges, evidence}`.
    - With `?dot=1` returns GraphViz DOT.

Planner:

- Reads `var/tuples.ndjson` and proves direct tuple presence as evidence.
- Nodes: tenant/subject/relation/resource. Edges mark `proven` or `missing`.

Debug UI:

- Static app in `public/role/debug/index.html` hitting `/v2/check/explain` and rendering a simple graph (SVG).

Notes:

- For production, connect Planner to your actual check() pipeline / data store.
- Keep SSE watch (D3) to drive cache invalidations; token is `filesize(var/tuples.ndjson)` for quick monotonic
  watermark.
