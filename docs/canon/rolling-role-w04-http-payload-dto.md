# Rolling Role W04 HTTP payload / DTO readiness

## Canon decision

Rolling keeps the 1+2 surface model:

- Admin surface may use native EasyAdmin controllers only under `src/Controller/Admin`.
- Front/public surface remains zero-controller and is consumed through Cruding + Viewing + Interfacing.
- Runtime HTTP services may remain under `src/Service/Http`, but request payload parsing must move toward DTO-first seams.

## W04 scope

W04 is not a full API rewrite. It adds the first safe Symfony 8.1-oriented request payload seam and converts the highest-value HTTP endpoints that are already routable services.

## Added seam

- `App\Rolling\Service\Http\Request\JsonPayloadReader`
- `App\Rolling\DTO\Http\Role\RoleCheckPayload`
- `App\Rolling\DTO\Http\Role\AccessCheckPayload`
- `App\Rolling\DTO\Http\Role\RebacWritePayload`
- `App\Rolling\DTO\Http\Role\RebacCheckPayload`

## Converted endpoints

- `src/Service/Http/Role/Api/CheckHttpService.php`
- `src/Service/Http/Role/V2/AccessHttpService.php`
- `src/Service/Http/Role/V2/RebacHttpService.php`

These no longer parse request JSON inline with `json_decode($request->getContent(), true)`.

## Remaining work

The remaining manual parsing should be converted in small endpoint groups:

1. Admin runtime endpoints:
   - `Api/Admin/TenantAdminHttpService.php`
   - `Api/AdminHttpService.php`
2. Policy/security endpoints:
   - `Api/PolicyHttpService.php`
   - `Api/SecurityHttpService.php`
   - `Api/ResidencyHttpService.php`
3. Evaluation endpoints:
   - `Api/EvalHttpService.php`
   - `Api/PelEvalHttpService.php`
   - `Api/ExplainHttpService.php`
   - `Api/WhatIfHttpService.php`
4. Bulk/streaming endpoints should stay request-stream aware and should not be forced into JSON DTOs.

## Symfony 8.1 direction

Where Symfony controller argument mapping is appropriate, later waves may replace the local DTO hydration with `#[MapRequestPayload]` or `#[MapUploadedFile]`. For W04, the safer step is local DTO extraction because Rolling routes currently target service methods directly.
