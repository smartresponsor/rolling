# API errors & UX patterns

- Use HTTP 200 for decision responses with `{allowed:true|false}`.
- Use HTTP 4xx/5xx only for transport/config errors.
- Echo headers: `X-Role-Consistency`, optional `X-Role-Consistency-Token`.
- 429 formatting for quotas: include `X-RateLimit-Limit`, `X-RateLimit-Remaining`, `Retry-After`.
