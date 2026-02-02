# role-rc5-e1-pipeline

Created: 2025-10-27T18:22:21Z UTC

EN-only comments. Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Adds Role Decision Pipeline v1:

- RequestContext (subject, action, resource, attrs)
- Stage chain: context -> policy -> post
- Batch API: evaluate many requests
- Trace: collect explain steps (for E4)

Routes:

- POST /v2/role/eval
- POST /v2/role/eval-batch
