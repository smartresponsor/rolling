# Rolling W01 Surface Policy

Rolling has two intentional UI roads.

## Admin surface

EasyAdmin is allowed natively inside Rolling under `src/Controller/Admin/`.

Admin controllers may configure dashboards, CRUD fields, filters, actions, menus, and delegate into services. They must not own policy decisions, persistence orchestration, or duplicated business logic.

## Front/public surface

The front/public surface is zero-controller and must go through the host surface stack: Cruding -> Rolling service/capability -> Viewing -> Interfacing.

`zero-controllers` means zero public/generic controllers. EasyAdmin controllers are allowed only under `src/Controller/Admin`.
