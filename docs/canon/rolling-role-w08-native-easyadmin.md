# Rolling W08 native EasyAdmin surface

Rolling is a full business surface with two UI/runtime sides:

- Admin/backoffice: native EasyAdmin under `src/Controller/Admin/*`.
- Front/public: zero public controllers, consumed through Cruding, Viewing, and Interfacing.

## Controller canon

Allowed:

```text
src/Controller/Admin/RollingDashboardController.php
src/Controller/Admin/*CrudController.php
```

Forbidden:

```text
src/Controller/Api/*
src/Controller/V2/*
src/Controller/*Controller.php outside src/Controller/Admin
```

## W08 scope

This wave adds only native EasyAdmin entrypoints for persisted Rolling ACL entities. It does not reintroduce generic controllers and does not move runtime HTTP/API endpoints back from `src/Service/Http/*`.

## Read-only boundaries

Mutation execution events are exposed read-only in EasyAdmin. They are execution metadata and should be written by the Rolling execution/audit services, not manually edited from the admin UI.
