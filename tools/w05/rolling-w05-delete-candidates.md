# Rolling W05 delete candidates — PowerShell plan

This is a safe delete-candidate scanner for the Rolling 1+2 surface model.

Canon:

```text
Rolling/src/Controller/Admin/*  ✅ native EasyAdmin only
Rolling/src/Controller/*        ❌ delete/transform candidate unless under Admin
Generated var/cache/log files   ❌ not source snapshot content
```

Run from the Rolling repository root:

```powershell
.\tools\w05\rolling-w05-delete-candidates.ps1 -RootPath .
```

Safe delete dry-run:

```powershell
.\tools\w05\rolling-w05-delete-candidates.ps1 -RootPath . -Apply -WhatIf
```

Apply only safe generated/runtime cleanup:

```powershell
.\tools\w05\rolling-w05-delete-candidates.ps1 -RootPath . -Apply
```

Review-required destructive dry-run:

```powershell
.\tools\w05\rolling-w05-delete-candidates.ps1 -RootPath . -Apply -IncludeReviewRequired -WhatIf
```

Do not run `-IncludeReviewRequired` without reading the generated Markdown/CSV report first.
