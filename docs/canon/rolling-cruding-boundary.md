# Rolling Cruding boundary

Cruding is the canonical owner of generic CRUD runtime for Symfony host applications.

Rolling owns role, permission, ACL, ReBAC, PDP, policy, audit, explain, obligation, hierarchy, security, tenancy, and administration business semantics. Rolling must not duplicate generic CRUD routing or CRUD controllers once Cruding is available as the platform CRUD owner.

## Cruding-owned responsibilities

Cruding owns generic CRUD route grammar, operation-token routing, generic CRUD controllers, resource workbench contract assembly, `CrudResourceContract`, generic CRUD fallback behavior, and reserved route-token protection.

Generic operations owned by Cruding include:

```text
index
show
new
edit
delete
archive
restore
import
export
```

Cruding public surface includes:

```text
App\Cruding\Controller\Crud\CrudController
App\Cruding\Controller\Crud\CrudIndexController
App\Cruding\Controller\Crud\CrudShowController
App\Cruding\Controller\Crud\CrudCreateController
App\Cruding\Controller\Crud\CrudEditController
App\Cruding\Controller\Crud\CrudDeleteController
App\Cruding\Value\Resource\CrudResourceContract
```

## Rolling-owned responsibilities

Rolling may provide Cruding with resource-specific Doctrine entities, repositories, form types, validation rules, business services, resource metadata, domain actions, and safe result DTOs.

Rolling may keep business routes and services for actions such as:

```text
approve
reject
delegate
override
publish
check
evaluate
explain
apply
review
rotate
sign
verify
backup
restore-tenant
enforce
shadow-compare
```

Those routes are business operations, not generic CRUD routes.

## Current Rolling migration candidates

The current EasyAdmin admin surface is transitional and should migrate to Cruding:

```text
src/Controller/Admin/RollingRoleCrudController.php
src/Controller/Admin/RollingRolePermissionCrudController.php
src/Controller/Admin/RollingSubjectRoleAssignmentCrudController.php
src/Controller/Admin/RollingAclRuleCrudController.php
src/Controller/Admin/RollingAclMutationExecutionEventCrudController.php
src/Controller/Admin/RollingDashboardController.php
config/routes/rolling_admin_easyadmin.yaml
```

`RollingPermissionCrudController` is no longer linked from the dashboard and no longer references missing `componentName` or `description` fields. It remains only because repository tooling blocked file deletion during this slice.

## Dependency rule

Rolling should depend on `cruding/crud` only when it directly references Cruding public contracts or registers Cruding resource providers. The dependency must be committed together with a synchronized `composer.lock` update.

Do not add a Composer dependency without updating the lock file.

## Migration sequence

1. Keep current EasyAdmin files only as transitional surface.
2. Add report-first audits that list EasyAdmin CRUD controllers as Cruding migration candidates.
3. Introduce Rolling resource metadata/provider contracts for Cruding.
4. Add `cruding/crud` dependency with lock update.
5. Translate `RollingCrudResourceDefinition` into Cruding provider registrations.
6. Remove Rolling EasyAdmin CRUD controllers and route import after Cruding parity.
7. Keep only business action routes/controllers/services in Rolling.

The target state is zero generic CRUD controllers and zero generic CRUD routes in Rolling.
