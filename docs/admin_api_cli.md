# Admin API / CLI

Admin operations are exposed through the canonical Symfony-first application surface.

Primary runtime pieces:
- admin controllers under `src/Controller/Api/Admin/`
- admin security classes under `src/Security/Admin/`
- policy registry infrastructure under `src/Infrastructure/Policy/Registry/`
- native Symfony console commands wired through `bin/console`

Metrics and audit output should be treated as infrastructure concerns under canonical `App\Infrastructure\...` and `App\Service\...` layers rather than legacy namespaces.
