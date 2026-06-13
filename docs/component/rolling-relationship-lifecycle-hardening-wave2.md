# Rolling relationship/lifecycle hardening wave 2

Status: applied as a conservative hardening pass.

## Scope

- Adds `RoleAssignmentLifecyclePolicy`.
- Keeps lifecycle validation string-based to avoid schema drift.
- Does not touch `*EnGb*` / translation normalization.
- Does not touch Attachment/Attaching mechanics.

## Lifecycle decision

Role/ACL lifecycle. Security policy rows remain local security lifecycle records.

## Transition map

- `proposed` -> `active`, `rejected`
- `active` -> `suspended`, `revoked`, `expired`
- `suspended` -> `active`, `revoked`, `expired`
- `rejected` -> `terminal`
- `revoked` -> `terminal`
- `expired` -> `terminal`
