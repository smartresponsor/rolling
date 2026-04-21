# Admin API / CLI

Admin operations are exposed by the package and become active only after the host Symfony application registers the bundle and imports the package route files.

Primary runtime pieces:
- admin controllers under `src/Controller/Api/Admin/`
- admin security classes under `src/Security/Admin/`
- policy registry infrastructure under `src/Infrastructure/Policy/Registry/`
- package console surface built from `App\Rolling\Infrastructure\Console\RoleConsoleApplication` and Symfony commands discovered from the bundle container

This repository is not a standalone app runtime anymore. `bin/console` belongs to the consuming host application, not to this package.

Metrics and audit output should be treated as infrastructure concerns under canonical `App\Rolling\Infrastructure\...` and `App\Rolling\Service\...` layers rather than legacy namespaces.
