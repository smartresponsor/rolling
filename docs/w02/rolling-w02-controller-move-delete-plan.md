# Rolling W02 — PS move/delete plan

This patch-kit adds a PowerShell planning tool for the Rolling 1+2 surface cleanup.

Canon decision:

```text
Rolling/src/Controller/Admin/*  allowed for native EasyAdmin CRUD/Dashboard
Rolling/src/Controller/Api/*    not allowed as front/public controller ownership
Rolling/src/Controller/V2/*     not allowed as front/public controller ownership
Rolling/src/Controller/*        not allowed unless under Admin
```

The script is intentionally read-only by default and classifies existing controllers/routes as `ReviewRequired`, because current controllers are JSON/runtime controllers, not native EasyAdmin controllers.

## Usage

```powershell
.\tools\w02\rolling-w02-controller-move-delete-plan.ps1 -RootPath .
```

Safe hygiene-only dry run:

```powershell
.\tools\w02\rolling-w02-controller-move-delete-plan.ps1 -RootPath . -Apply -WhatIf
```

Full destructive dry run after manual review:

```powershell
.\tools\w02\rolling-w02-controller-move-delete-plan.ps1 -RootPath . -Apply -IncludeReviewRequired -IncludeRoutes -WhatIf
```

The generated files are written under:

```text
var/w02-controller-plan/rolling-w02-controller-move-delete-plan.csv
var/w02-controller-plan/rolling-w02-controller-move-delete-plan.md
```
