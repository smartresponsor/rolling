# Rolling / Role ACL market coverage and growth plan

Status: RC growth boundary, not a runtime migration wave.

## Business responsibility boundary

Rolling owns authorization business capability for Role ACL, policy decision, and relationship-aware access evaluation.

In scope:

- Role ACL grants, policy registry, and policy evaluation.
- ReBAC tuple checks for subject / relation / object decisions.
- PDP-style decision interfaces and HTTP-facing decision services.
- Batch decision evaluation and performance regression guardrails.
- Explain, audit, metrics, cache, consistency headers, and operational console diagnostics.
- SoD and four-eyes approval gates for privileged ACL mutation paths.
- Tenant, mask, residency, and obligation semantics that affect authorization decisions.
- Symfony bundle integration and package-owned route/service metadata.

Out of scope:

- Authentication and identity proofing.
- Host application CRUD screens, Twig CRUD templates, and host-specific admin flows.
- Generic user management outside authorization decision context.
- Billing, messaging, cataloging, or product ownership rules that do not directly affect authorization.
- Alternative architectural skeletons; Rolling remains Symfony-oriented under `App\\Rolling`.

## Current capability coverage

Covered in repository shape:

- RBAC/ACL primitives through role, grant, scope, and permission key concepts.
- ReBAC tuple storage and checking through canonical infrastructure and policy services.
- External PDP integration seams such as OPA/OpenFGA-like clients without making them the canonical execution home.
- Audit/explain output attached to decision responses.
- Batch checks and performance profile/regression tooling.
- Symfony-native bundle, DI, routes, console commands, and package docs.
- Housekeeping for audit and replay nonce stores.
- SoD and four-eyes approval workflows for sensitive ACL operations.

Known open growth points:

- Typed authorization model validation comparable to OpenFGA/SpiceDB schema discipline.
- ListObjects and ListUsers style reverse queries for subject/object discovery.
- Conditional relationship tuples / caveats for runtime context such as time, IP range, tenant risk, or approval state.
- Derived contextual roles comparable to policy engines that augment broad RBAC roles with runtime attributes.
- Policy test fixtures and golden authorization matrices for admin-visible confidence.
- Decision simulation reports for field-level and tenant-level authorization changes.
- Consistency/revision semantics as a first-class response contract for tuple writes and reads.
- Authorization data import/export compatibility profiles for migration from existing ACLs.

## Market comparison

### OpenFGA / Zanzibar family

Market signal:

- Strong relationship tuple model.
- Authorization models define types and relations.
- Check, ListObjects, and ListUsers are expected API patterns.
- Conditional relationship tuples are becoming baseline capability for contextual decisions.

Rolling position:

- Rolling already has ReBAC tuple checks, batch decisions, and policy execution.
- Rolling should grow typed model validation and reverse query APIs before exposing broader admin tooling.

### SpiceDB / Authzed family

Market signal:

- Schema definitions, relations, permissions, caveats, arrows, wildcards, and typechecking are core differentiators.
- Explicit typechecking catches authorization model bugs before runtime.

Rolling position:

- Rolling has a model schema registry and validator, but the validator should become stricter around subject types, relation names, and permission expressions.
- Rolling should keep the Symfony package as the canonical execution home rather than introducing a separate schema service tree.

### Cerbos family

Market signal:

- Resource policies, principal policies, role policies, derived roles, conditions, schemas, testing, and debugging are product-visible capabilities.

Rolling position:

- Rolling already has policy services, explain traces, and obligation output.
- Rolling should add derived-role vocabulary and policy test reporting while keeping host CRUD outside the package.

### Oso family

Market signal:

- Policy-as-code, authorization facts, local authorization, and client SDK enforcement are treated as first-class developer experience.

Rolling position:

- Rolling has SDK material, service-level decisions, and local bundle integration.
- Rolling should improve import/export, fixture-driven policy testing, and local decision simulation.

### Casbin family

Market signal:

- Simple embeddable authorization models are popular for framework-level adoption.

Rolling position:

