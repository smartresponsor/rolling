# Consistency & Watch (RC-D3)

## Read modes

- `eventual` (default) and `strong`. Client may pass `?consistency=strong` or header `X-Role-Consistency: strong`.
- Response echoes mode in `X-Role-Consistency`. If available, `X-Role-Consistency-Token: <opaque>` is returned.

## Watch stream (SSE)

- Endpoint: `GET /v2/tuples/watch?offset=<bytes>`
- Server returns `text/event-stream`, events type `tuple`, `id` contains next byte offset for resubscribe.
- Backpressure: client should use EventSource with retry or resume with `?offset=<last-id>`.

## Cache invalidation

- Local in-proc cache keyed by `{tenant}:{subject}:{relation}:{resource}:{mode}`.
- On tuple write, invalidate both `strong` and `eventual` entries for the affected key.

## Budgets (dev target)

- p95 /check(eventual) ≤ 5–7ms (no network), p99 ≤ 15ms with hot cache.
- Watch end-to-end invalidation ≤ 1s p95 on local stack.
