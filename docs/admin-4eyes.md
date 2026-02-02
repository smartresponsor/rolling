# Admin 4-Eyes Workflow

- Two independent approvals required before a role grant is applied.
- Interfaces live under `ServiceInterface/Role/Admin/**` and `InfraInterface/Role/Admin/**`.
- InMemory repository provided for demo; wire DB in production.

## Quick start

```
php tools/admin/four_eyes_demo.php
# -> creates request, approves by A and B, writes report/grants_applied.ndjson
```
