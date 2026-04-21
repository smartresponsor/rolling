# Integrations

Register the canonical Symfony bundle in the host application and import package-owned routes from the bundle path.

## Host application registration

Add the bundle class in the host app:
- `App\Rolling\Infrastructure\Symfony\RoleBundle::class`
- host `config/bundles.php`

## Host application imports

Recommended host imports:
- service import in host `config/services.yaml` when host-level overrides are needed
- route import from `@RoleBundle/config/routes/*.yaml` or a narrower subset such as `@RoleBundle/config/routes/role.yaml`

Example host route import:

```yaml
role_bundle_routes:
  resource: '@RoleBundle/config/routes/'
  type: glob
```

Bundle-owned route files under this package are the active source of truth. Older wrapper names from previous recovery waves are historical only and should not be used as active integration targets.


## Package HTTP surface policy

This package does not ship CRUD web controllers or CRUD mutation routes by default for host-facing model administration.

Keep in the package:
- entities, value objects, DTOs
- reusable form types only when they are truly host-agnostic
- domain and application services
- voters or policy helpers when they are reusable
- metadata and service wiring that are part of the package contract

Keep in the host application:
- CRUD page controllers
- CRUD page routes
- Twig CRUD templates
- host-specific redirects and ownership UI logic
- host-specific mutation endpoints for model administration
