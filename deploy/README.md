# Rolling deploy surface

Rolling keeps runtime/deployment-owned files under `deploy/`.

## Canonical local compose entrypoint

Use the deploy-owned compose file when validating the deploy surface directly:

```bash
docker compose -f deploy/compose/compose.yaml up --build
```

## Root compose shim

The repository root may keep `compose.yaml` as a developer-experience shim for the conventional command:

```bash
docker compose up --build
```

The root shim must stay equivalent to `deploy/compose/compose.yaml` and must not become the canonical owner of deploy configuration.

## Docker image files

Dockerfile and entrypoint ownership stays in `deploy/docker/`:

- `deploy/docker/Dockerfile`
- `deploy/docker/entrypoint.sh`
