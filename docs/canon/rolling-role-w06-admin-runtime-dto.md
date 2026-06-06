# Rolling W06 Admin/Runtime DTO Triage

Rolling keeps two explicit surfaces:

- Admin/runtime HTTP endpoints may stay under `src/Service/Http/*` when they are not native EasyAdmin controllers.
- Native EasyAdmin controllers are allowed only under `src/Controller/Admin/*` and are not created in this wave.

W06 continues Symfony 8.1 readiness by removing manual request-body parsing from the remaining high-value admin/runtime HTTP services.

## Rules

- HTTP services read JSON through `JsonPayloadReader`.
- Endpoint payload shape is represented by immutable DTOs under `src/DTO/Http/Role/*`.
- Front/public controllers remain forbidden.
- `Api/Admin*` remains HTTP/runtime, not native EasyAdmin.
- Native EasyAdmin surface is a later wave.

## Converted endpoint buckets

- Admin approval workflow payloads.
- Tenant admin quota/backup/restore payloads.
- Pipeline eval/explain/PEL/what-if payloads.
- Obligation apply/check-and-apply payloads.
- Separation-of-duties payloads.
- Debug policy shadow payloads.

## Remaining follow-up

- Checkbox/null semantics still need targeted tests.
- `WatchHttpService`, `BulkHttpService`, and `PermCatalogHttpService` are not body-payload endpoints and were not converted.
- Deep DI cleanup for local policy pipeline factories remains separate from payload DTO migration.
