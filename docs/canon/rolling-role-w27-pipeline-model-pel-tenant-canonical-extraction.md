# Rolling Role w27 — pipeline/model/pel/tenant canonical extraction

## What moved into canonical App layers

- `src/Service/Pipeline/*`
- `src/Service/Model/*`
- `src/Service/Pel/*`
- `src/Service/Tenant/Backup.php`
- `src/Service/Tenant/Limits.php`
- `src/Service/Tenant/Quota.php`
- `src/Service/Tenant/Restore.php`
- `src/ServiceInterface/Model/SchemaStorageInterface.php`
- `src/ServiceInterface/Pipeline/StageInterface.php`
- `src/ServiceInterface/Tenant/*`

## What became bridge-zone

Legacy files in:
- `src/Legacy/Service/Pipeline/*`
- `src/Legacy/Service/Model/*`
- `src/Legacy/Service/Pel/*`
- `src/Legacy/Service/Tenant/{Backup,Limits,Quota,Restore}.php`

now point at canonical App-layer classes instead of keeping primary implementations.

## Runtime consumers switched to canonical App layer

- `src/Controller/Api/ModelController.php`
- `src/Controller/Api/EvalController.php`
- `src/Controller/Api/ExplainController.php`
- `src/Controller/Api/PelEvalController.php`
- `src/Controller/Api/WhatIfController.php`
- `src/Controller/Api/Admin/TenantAdminController.php`
- `tests/Role/Model/DiffTest.php`
- `misc/repo/tools/model_apply.php`
- `misc/repo/tools/model_diff.php`
- `misc/repo/tools/backup_tenant.php`
- `misc/repo/tools/restore_tenant.php`
