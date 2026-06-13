# Rolling entity-first migration retirement

## Decision

Rolling is the next component retired from schema-first ownership in the current platform slice.

The following schema files are no longer authoritative:

- `Rolling/migrations/**`
- `Rolling/ops/db/sqlite/acl_administration_tables.sql`
- `Rolling/ops/db/sqlite/role_audit.sql`

## Entity-first coverage

The ACL administration migration was already mostly represented by Doctrine entities:

- `rolling_permission` -> `RollingPermission`
- `rolling_role` -> `RollingRole`
- `rolling_acl_rule` -> `RollingAclRule`
- `rolling_subject_role_assignment` -> `RollingSubjectRoleAssignment`
- `rolling_role_permission` -> `RollingRolePermission`
- `rolling_role_hierarchy` -> `RollingRoleHierarchy`
- `rolling_acl_mutation_execution_event` -> `RollingAclMutationExecutionEventEntity`

The remaining SQL-only table is now represented as:

- `role_audit` -> `RollingRoleAuditEntity`

## Metadata reconciliation

Migration-owned indexes and unique constraints were moved into Doctrine attributes on the entities.  The missing `RollingRoleHierarchyRepository` and repository contracts for ACL entities were added.

## Objecting decision

No Objecting embeddables were added in this step. Rolling ACL rows are runtime/security policy records, not generic long-lived business aggregates. Fields such as `assignedAt`, `createdAt`, `ts`, `enabled`, `decision`, `scopeKey`, and `status` remain local lifecycle/security facts rather than duplicated generic object system fields.

## Legacy monolith

`Entity-src(6).zip` was checked. There is no old Rolling/Role aggregate with additional Doctrine relations to restore. Legacy cross-component user/admin links were not introduced as ORM couplings.
