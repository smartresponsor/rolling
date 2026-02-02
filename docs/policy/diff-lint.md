# Policy Diff + Lint + Migration (G4)

This package provides three CLI tools:

- `tools/policy/diff.php <src> <dst>` → `report/policy_diff_*.json`
- `tools/policy/lint.php <spec>` → `report/policy_lint_*.json` (non-zero exit on errors)
- `tools/policy/migration.php <src> <dst>` → `report/policy_migration_*.yaml`

Supported PEL v1 rules and expressions:

- `subject.roles contains <role>`
- `action == <verb>`
- `subject.id == resource.ownerId`
- `resource.type in [doc,project]`

Lint rules:

- unique `id` with `^[a-z0-9_.-]+$`
- `effect` must be `allow|deny`
- `when` must be a list of supported expressions
- `reason` recommended (warning if empty)

Usage examples:

```bash
php tools/policy/diff.php policy/policy_v1.pel.json policy/policy_v2_shadow.pel.json
php tools/policy/lint.php policy/policy_v1.pel.json
php tools/policy/migration.php policy/policy_v1.pel.json policy/policy_v2_shadow.pel.json
```
