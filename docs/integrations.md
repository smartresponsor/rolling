# Integrations (RC-D6)

## Envoy ext_authz

- Adapter: `src/Http/Role/Adapters/Envoy/` (Go).
- Envoy config: `deploy/integrations/envoy/envoy.yaml`.
- Compose: `deploy/integrations/docker-compose.yml` (services: adapter, envoy, backend=httpbin).

Quick demo:

```bash
cd deploy/integrations/envoy && bash demo.sh
# Expect: 200 with 'x-role-check: allow', and 403 when header x-role-debug-deny: 1 is present
```

## Oathkeeper example

- `deploy/integrations/oathkeeper/rules.yaml` uses `remote_json` authorizer pointing to `adapter-http` (not included).
- For SR/Role, map it to your `/v2/check` endpoint and shape JSON accordingly.

## Symfony bundle

- Namespace: `App\Infrastructure\Symfony\RoleBundle` (legacy bridge aliases remain available during migration).
- Config keys live under canonical `role.pdp.*` and `role.security.*` trees via `App\Infrastructure\Symfony\DependencyInjection\Configuration`.
- Primary HTTP access entrypoint is `App\Controller\V2\AccessController`; legacy integration bundle classes remain bridge-only during migration.
- Register the canonical bundle and config; legacy `App\Integration\Symfony\...` / `App\Legacy\Http\SymfonyBundle\...` names are bridge-only compatibility paths.

All pieces comply with layer-first isolation and can be moved under your mono-repo roots.
