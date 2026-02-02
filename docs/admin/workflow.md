# Admin Workflow (RC5 E9) — four-eyes, delegation, override

- Storage:
    - approvals: `var/admin/approvals/<id>.json`, audit: `var/admin/audit/admin.ndjson`
    - approver directory: `var/admin/approvers.json` ({"t1": {"allow":["user:boss","user:sec"]}})
    - delegations: `var/admin/delegations.json`
    - override policy: `var/admin/override.json` ({"t1": {"allow":["user:cto"]}})

- Endpoints:
    - `POST /v2/admin/approval/start` {tenant, relation, resource, requester, opts:{required,distinctBy,title}}
    - `POST /v2/admin/approval/approve` {id, subject, comment}
    - `POST /v2/admin/approval/reject`  {id, subject, reason}
    - `POST /v2/admin/delegate`         {tenant, from, to, until, scope}
    - `POST /v2/admin/override`         {id, actor, reason}

- Notes:
    - SoD: requester cannot approve own request.
    - Approver must be in directory or be a valid delegate (not expired).
    - N-of-M: once approvals >= required → status=approved.
    - Override requires explicit allow in `override.json`.

Generated: 2025-10-27T18:03:17Z UTC
