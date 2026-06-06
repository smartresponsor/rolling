# Rolling W01 Surface Policy

Rolling is a full business surface with two intentional UI roads.

## Admin surface

EasyAdmin is allowed natively inside Rolling.

Allowed controller location:

```text
src/Controller/Admin/
```

Allowed code type examples:

```text
src/Controller/Admin/RollingDashboardController.php
src/Controller/Admin/RoleCrudController.php
src/Controller/Admin/PolicyCrudController.php
src/Controller/Admin/PermissionCrudController.php
```

Admin controllers must stay thin. They may configure dashboards, CRUD fields, filters, actions, menus, and delegate into services. They must not own policy decisions, persistence orchestration, or duplicated business logic.

## Front/public surface

The front/public surface is zero-controller.

Forbidden locations for public/front ownership:

```text
src/Controller/Api/
src/Controller/V2/
src/Controller/Observability/
src/Controller/*Controller.php
```

Front flow must go through the host surface stack:

```text
Cruding -> Rolling service/capability -> Viewing -> Interfacing
```

## Runtime/internal surface

Runtime HTTP endpoints are allowed only when explicitly classified and wired as runtime surface. They must not be used as a fallback for public/front UI.

Preferred runtime shape:

```text
src/Service/Http/<Capability>/<Capability>HttpService.php
```

## W01 freeze rule

`zero-controllers` means zero public/generic controllers. EasyAdmin controllers are allowed only under `src/Controller/Admin`.
