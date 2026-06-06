# Rolling Role W05 Policy/Security DTO and DI Cleanup

W05 continues the Symfony 8.1 readiness direction from W03-W04.

## Canon

- HTTP services may remain routable services.
- Payload parsing is centralized through `JsonPayloadReader`.
- Endpoint payloads are represented by small readonly DTOs under `src/DTO/Http/Role`.
- Services touched by payload DTO migration must not build their infrastructure dependencies with local `new`.
- Filesystem stores remain infrastructure implementations and are wired through Symfony DI.

## Touched endpoint seams

- `PolicyHttpService`
- `SecurityHttpService`
- `ResidencyHttpService`

## Not in scope

- Native EasyAdmin CRUD layer.
- Front surface template/view ownership.
- Full MapRequestPayload conversion.

The next step may continue with admin tenant/runtime endpoints or finish direct `new` removal in other HTTP services.
