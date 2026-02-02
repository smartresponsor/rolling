# ReBAC Hardening: Namespaces, Constraints, Tenant Boundary (G5)

**Goal:** strengthen graph-based authorization with explicit namespaces, allowed transitions, and strict tenant
isolation.

## Namespaces

- `subject` → identity & membership edges (e.g., `u1 member g1`)
- `group` → role/group relations (e.g., `g1 grants perm.read`)
- `permission` → capability nodes (e.g., `perm.read allows doc:*`)
- `resource` → protected objects (e.g., `doc:1`)

## Constraints

- Allowed transitions: subject→group→permission→resource (configurable).
- Tenant boundary: by default **enforced**, disallow cross-tenant traversals.

## API

- `GraphStoreInterface::checkAccess($tenant,$startNs,$subject,$relation,$object,$constraints): bool)`
- Traversal searches for a path whose **final hop** has the requested `$relation` into `$object`.

## Demo

```
php tools/rebac/parity_check.php
# → report/rebac_parity.json
# Expect:
#  sameTenantAllowed=true
#  crossTenantDenied=true
#  disallowedNamespaceDenied=true
```
