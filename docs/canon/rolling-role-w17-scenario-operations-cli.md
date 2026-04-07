# Rolling / Role w17 — scenario operations CLI

This wave extends the w16 foundation with actual scenario operations for propagation and elimination.

Delivered in this wave:

- richer fixture format using `seed` + named `scenarios`
- scenario runner methods for baseline / preview / run
- CLI commands for propagation and elimination previews and runs
- PHPUnit coverage for scenario execution and CLI output contracts

No canonical placement rollback was introduced. Legacy trees remain quarantined under `src/Legacy/...`.
