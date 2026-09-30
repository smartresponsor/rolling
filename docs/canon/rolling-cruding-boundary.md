# Rolling Cruding boundary

Cruding is the canonical owner of generic CRUD runtime for Symfony host applications.

Rolling owns role, permission, ACL, ReBAC, PDP, policy, audit, explain, obligation, hierarchy, security, tenancy, and administration business semantics. Rolling must not duplicate generic application CRUD routing or CRUD processing once Cruding is available as the platform CRUD owner. Canon021 explicitly permits native EasyAdmin administrative/back-office CRUD controllers and routes; that surface does not compete with Cruding.

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

## Native EasyAdmin administrative surface

The current EasyAdmin admin surface is a permitted Canon021 exception and may coexist with Cruding:

```text
src/Controller/Admin/RollingRoleCrudController.php
src/Controller/Admin/RollingRolePermissionCrudController.php
src/Controller/Admin/RollingSubjectRoleAssignmentCrudController.php
src/Controller/Admin/RollingAclRuleCrudController.php
src/Controller/Admin/RollingAclMutationExecutionEventCrudController.php
src/Controller/Admin/RollingDashboardController.php
config/routes/rolling_admin_easyadmin.yaml
```

## Dependency rule

Rolling should depend on `cruding/crud` only when it directly references Cruding public contracts or registers Cruding resource providers. The dependency must be committed together with a synchronized `composer.lock` update.

Do not add a Composer dependency without updating the lock file.

## Integration sequence

1. Keep native EasyAdmin files as the administrative/back-office surface.
2. Audit them as the explicit Canon021 exception, not as generic CRUD ownership drift.
3. Maintain Rolling resource metadata/provider contracts for Cruding.
4. Keep the `cruding/crud` dependency synchronized with Composer metadata when Rolling consumes its public contracts.
5. Translate `RollingCrudResourceDefinition` into Cruding provider registrations where generic application CRUD delivery is required.
6. Keep business action routes/controllers/services in Rolling.

The target state is zero component-local generic application CRUD duplication. Native EasyAdmin back-office CRUD remains allowed and is not an RC migration blocker.
