# Four-eyes admin workflow (G7)

Goal: enforce segregation-of-duties (SoD) with a minimal four-eyes gate.

- When policy allows a sensitive action (e.g., delete on doc), gate decision with `approvalId`.
- Second actor approves via `tools/approval/approve.php <id> [actor]`.
- `ApprovalGate::resolve(id)` returns final allowed decision.

Config notes:

- Current rule: action=delete on resource.type=doc; skip role=admin.
- Store: file-based under `var/approval/case_*.json`.

Demo:

```
php tools/approval/demo.php
# → report/approval_demo_step1.json (pending) and step2.json (approved)
```
