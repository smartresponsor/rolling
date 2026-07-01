# Rolling Objecting adoption

Rolling depends on `objecting/object` and must not reimplement platform system fields locally.

Objecting owns reusable field packs through `App\Objecting` embeddables, traits, and interfaces. Rolling entities may keep business-specific fields, but generic lifecycle, state, soft-delete, version, identity, and audit fields must come from Objecting.

## Dependency status

Rolling already declares the required package dependency:

```json
"objecting/object": "dev-master"
```

The repository also uses a local path repository to `../Objecting`, which is correct for workspace development mode.

## Objecting packs relevant to Rolling

Relevant Objecting contracts and traits:

- `ObjectAuditedInterface` + `ObjectAuditEmbeddableTrait` for `object_created_at`, `object_modified_at`, `object_created_by`, `object_modified_by`;
- `ObjectStatefulInterface` + `ObjectStateEmbeddableTrait` for `object_active`, `object_enabled`, `object_status`;
- `ObjectVersionedInterface` + `ObjectVersionEmbeddableTrait` for `object_version`, `object_etag`;
- `ObjectSoftDeletableInterface` + `ObjectSoftDeleteEmbeddableTrait` for `object_deleted`, `object_deleted_at`, `object_deleted_by`.

## Rolling rule

Rolling must use Objecting packs when a field is generic platform state:

- lifecycle creation/modification metadata;
- generic enabled/active/status state;
- deletion metadata;
- optimistic object version or ETag;
- generated object UUID/slug identity.

Rolling may keep local fields when the field is a business fact, not a generic system field. Examples:

- ACL mutation execution `status` is an execution result status, not generic object lifecycle status;
- ACL mutation execution `created_at` can be a domain event timestamp when it represents when the captured execution happened;
- role keys, permission keys, scope keys, subject identifiers, and policy effects are Rolling business data.

## Current findings

Current Rolling sources declare the `objecting/object` dependency, but `src/` does not yet use `App\Objecting\...` traits or interfaces.

Known local system-field candidates:

- `RoleEntity.enabled` should move to Objecting state pack unless it is deliberately split from `object_enabled` as a business-specific role flag;
- `RoleAclRuleEntity.enabled` should move to Objecting state pack unless it remains a rule-evaluation business switch;
- `RoleHierarchyEntity.enabled` should move to Objecting state pack unless it remains an edge-specific business switch;
- generic future `created_at`, `modified_at`, `deleted_at`, `version`, and `etag` fields must not be added locally.

Known likely business fields that should not be blindly migrated:

- `RoleAclMutationExecutionEventEntity.status`;
- `RoleAclMutationExecutionEventEntity.created_at`;
- `RoleAclMutationExecutionEventEntity.succeeded`.

Those fields describe an immutable execution event payload and should be reviewed separately before any schema migration.

## Migration approach

1. Keep the composer dependency.
2. Add a report-first audit for duplicated system fields.
3. Classify every reported field as either Objecting-owned system state or Rolling-owned business state.
4. Migrate confirmed system fields to Objecting traits/interfaces.
5. Preserve business aliases only when needed for backward compatibility, and remove them after callers are updated.
6. Add mapping/schema checks before turning the audit into a hard gate.
