# Rolling W08 native EasyAdmin surface

Rolling is a full business surface with two UI/runtime sides:

- Admin/backoffice: native EasyAdmin under `src/Controller/Admin/*`.
- Front/public: zero public controllers, consumed through Cruding, Viewing, and Interfacing.

## Controller canon

Allowed: `src/Controller/Admin/RollingDashboardController.php` and `src/Controller/Admin/*CrudController.php`.

Forbidden: public API/V2/front controllers outside `src/Controller/Admin`.

## W08 scope

This wave adds only native EasyAdmin entrypoints for persisted Rolling ACL entities. It does not reintroduce generic controllers and does not move runtime HTTP/API endpoints back from `src/Service/Http/*`.
