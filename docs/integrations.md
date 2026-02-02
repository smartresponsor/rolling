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

- Namespace: `App\Http\Role\SymfonyBundle`.
- Config keys: `role.endpoint`, `role.hmac_key`, `role.timeout_ms`.
- Client class: `App\Http\Role\Client` (`check(subject, relation, resource, context)`).
- Register bundle and set config in your app, then DI will wire `Client`.

All pieces comply with layer-first isolation and can be moved under your mono-repo roots.
