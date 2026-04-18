# Rollin wave 29 recovery

RC polish wave aligned with locally confirmed green state.

Changes:
- made `PdoAuditWriterTest` self-checking against relocated SQLite schema in `ops/db/sqlite/role_audit.sql`
- made `RoleCliMigrationParityTest` self-contained by generating a temporary policy fixture instead of depending on removed `misc/example-policy.json`

Intent:
- preserve green PHPUnit state in the repository snapshot itself
- eliminate brittle test dependencies on moved/removed root fixtures
