<#
.SYNOPSIS
  Rolling W02 controller move/delete planning tool.

.DESCRIPTION
  Produces an explicit move/delete plan for Rolling 1+2 surface canon:
  - Admin surface: native EasyAdmin controllers are allowed only under src/Controller/Admin/*.
  - Front surface: zero public controllers; Cruding + Viewing + Interfacing own front request/view/template flow.

  Default mode is read-only. It writes CSV/Markdown plan files and prints a summary.
  Use -Apply only after reviewing the generated plan.

.PARAMETER RootPath
  Repository root path. Defaults to current directory.

.PARAMETER OutDir
  Output directory for generated plan files. Defaults to var/w02-controller-plan.

.PARAMETER Apply
  Applies file deletions/moves listed as SafeToApply.
  Current plan intentionally marks existing controllers/routes as ReviewRequired, so Apply will not remove them unless
  -IncludeReviewRequired is also passed.

.PARAMETER IncludeReviewRequired
  Allows -Apply to execute ReviewRequired entries. This is intentionally explicit.

.PARAMETER IncludeRoutes
  Include route yaml files in the destructive apply set when -Apply is used.

.EXAMPLE
  .\tools\w02\rolling-w02-controller-move-delete-plan.ps1 -RootPath .

.EXAMPLE
  .\tools\w02\rolling-w02-controller-move-delete-plan.ps1 -RootPath . -Apply -WhatIf

.EXAMPLE
  .\tools\w02\rolling-w02-controller-move-delete-plan.ps1 -RootPath . -Apply -IncludeReviewRequired -IncludeRoutes -WhatIf
#>

[CmdletBinding(SupportsShouldProcess = $true)]
param(
    [string]$RootPath = (Get-Location).Path,
    [string]$OutDir = 'var/w02-controller-plan',
    [switch]$Apply,
    [switch]$IncludeReviewRequired,
    [switch]$IncludeRoutes
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

function Join-RepoPath {
    param([string]$RelativePath)
    return Join-Path $RootPath $RelativePath
}

function Test-RepoFile {
    param([string]$RelativePath)
    return Test-Path -LiteralPath (Join-RepoPath $RelativePath) -PathType Leaf
}

function Add-PlanRow {
    param(
        [System.Collections.Generic.List[object]]$Rows,
        [string]$Kind,
        [string]$Source,
        [string]$Action,
        [string]$Target,
        [string]$Surface,
        [string]$Safety,
        [string]$Reason
    )

    $Rows.Add([pscustomobject]@{
        Kind   = $Kind
        Source = $Source
        Action = $Action
        Target = $Target
        Surface = $Surface
        Safety = $Safety
        Exists = (Test-RepoFile $Source)
        Reason = $Reason
    }) | Out-Null
}

$RootPath = (Resolve-Path -LiteralPath $RootPath).Path
$OutputPath = Join-RepoPath $OutDir
New-Item -ItemType Directory -Force -Path $OutputPath | Out-Null

$rows = [System.Collections.Generic.List[object]]::new()

# Canon rule: only native EasyAdmin controllers may live under src/Controller/Admin/*.
# Existing src/Controller/Api/*, src/Controller/V2/*, Health, Observability are not native EasyAdmin CRUD/Dashboard classes.
# They are classified for review, not blind deletion.
$controllerRows = @(
    @{ Source = 'src/Controller/Api/Admin/TenantAdminController.php'; Surface = 'runtime-admin-json'; Reason = 'JSON admin tenant runtime endpoint, not native EasyAdmin CRUD/Dashboard. Convert to EA screen or service capability before removing route.' },
    @{ Source = 'src/Controller/Api/AdminController.php'; Surface = 'runtime-admin-json'; Reason = 'JSON admin approval workflow endpoint, not native EasyAdmin CRUD/Dashboard. Convert to EA action/form/service before removing route.' },
    @{ Source = 'src/Controller/Api/CheckController.php'; Surface = 'front-runtime'; Reason = 'Public/front check endpoint. Front should go through Cruding + Viewing + Interfacing; keep service capability only.' },
    @{ Source = 'src/Controller/Api/ContextController.php'; Surface = 'front-runtime'; Reason = 'Public/front context endpoint. Replace with service capability consumed by Cruding.' },
    @{ Source = 'src/Controller/Api/EvalController.php'; Surface = 'front-runtime'; Reason = 'Public/front eval endpoint. Replace with service capability consumed by Cruding.' },
    @{ Source = 'src/Controller/Api/ExplainController.php'; Surface = 'front-runtime'; Reason = 'Public/front explain endpoint. Replace with service capability consumed by Cruding.' },
    @{ Source = 'src/Controller/Api/ObligationController.php'; Surface = 'front-runtime'; Reason = 'Public/front obligations endpoint. Replace with service capability consumed by Cruding.' },
    @{ Source = 'src/Controller/Api/PelEvalController.php'; Surface = 'front-runtime'; Reason = 'Public/front PEL eval endpoint. Replace with service capability consumed by Cruding.' },
    @{ Source = 'src/Controller/Api/PolicyController.php'; Surface = 'front-runtime'; Reason = 'Public/front policy endpoint. Replace with service capability consumed by Cruding.' },
    @{ Source = 'src/Controller/Api/ResidencyController.php'; Surface = 'front-runtime'; Reason = 'Public/front residency endpoint. Replace with service capability consumed by Cruding.' },
    @{ Source = 'src/Controller/Api/SecurityController.php'; Surface = 'front-runtime'; Reason = 'Public/front security endpoint. Replace with service capability consumed by Cruding.' },
    @{ Source = 'src/Controller/Api/SodController.php'; Surface = 'front-runtime'; Reason = 'Public/front SOD endpoint. Replace with service capability consumed by Cruding.' },
    @{ Source = 'src/Controller/Api/WatchController.php'; Surface = 'front-runtime'; Reason = 'Public/front tuple watch endpoint. Replace with service capability consumed by Cruding.' },
    @{ Source = 'src/Controller/Api/WhatIfController.php'; Surface = 'front-runtime'; Reason = 'Public/front what-if endpoint. Replace with service capability consumed by Cruding.' },
    @{ Source = 'src/Controller/HealthController.php'; Surface = 'runtime-health'; Reason = 'Health endpoint is runtime-only. Keep only if Rolling intentionally exposes standalone runtime health; otherwise host owns it.' },
    @{ Source = 'src/Controller/Observability/MetricsController.php'; Surface = 'runtime-observability'; Reason = 'Metrics endpoint is runtime-only. Keep only if Rolling intentionally exposes standalone runtime metrics; otherwise host owns it.' },
    @{ Source = 'src/Controller/V2/AccessController.php'; Surface = 'front-runtime'; Reason = 'Public/front access v2 endpoint. Replace with service capability consumed by Cruding.' },
    @{ Source = 'src/Controller/V2/BulkController.php'; Surface = 'front-runtime'; Reason = 'Public/front bulk v2 endpoint. Replace with service capability consumed by Cruding.' },
    @{ Source = 'src/Controller/V2/DebugPolicyShadowController.php'; Surface = 'debug-runtime'; Reason = 'Debug endpoint. Should not remain as public Rolling controller; move to dev tooling or service capability.' },
    @{ Source = 'src/Controller/V2/PermCatalogController.php'; Surface = 'front-runtime'; Reason = 'Public/front permission catalog endpoint. Replace with service capability consumed by Cruding.' },
    @{ Source = 'src/Controller/V2/RebacController.php'; Surface = 'front-runtime'; Reason = 'Public/front ReBAC endpoint. Replace with service capability consumed by Cruding.' }
)

foreach ($item in $controllerRows) {
    Add-PlanRow -Rows $rows `
        -Kind 'controller' `
        -Source $item.Source `
        -Action 'delete-after-service-confirmation' `
        -Target '' `
        -Surface $item.Surface `
        -Safety 'ReviewRequired' `
        -Reason $item.Reason
}

# Route files are incomplete in current slice: path + methods but no controller/_controller.
# Under 1+2, public/front routes should be removed from Rolling; admin EA routes should be attribute/import under src/Controller/Admin/*.
$routeRows = @(
    @{ Source = 'config/routes/health.yaml'; Surface = 'runtime-health'; Reason = 'Runtime health route. Host-level concern unless Rolling standalone runtime is intentionally kept.' },
    @{ Source = 'config/routes/metrics.yaml'; Surface = 'runtime-observability'; Reason = 'Runtime metrics route. Host-level concern unless Rolling standalone runtime is intentionally kept.' },
    @{ Source = 'config/routes/role.yaml'; Surface = 'front-runtime'; Reason = 'Front access routes should be owned by Cruding, not Rolling routes.' },
    @{ Source = 'config/routes/role_admin.yaml'; Surface = 'runtime-admin-json'; Reason = 'Admin JSON routes are not native EasyAdmin. Convert to EA CRUD/action/form before removing.' },
    @{ Source = 'config/routes/role_bulk.yaml'; Surface = 'front-runtime'; Reason = 'Front bulk routes should be owned by Cruding.' },
    @{ Source = 'config/routes/role_check.yaml'; Surface = 'front-runtime'; Reason = 'Front check route should be owned by Cruding.' },
    @{ Source = 'config/routes/role_consistency.yaml'; Surface = 'front-runtime'; Reason = 'Front/watch route should be owned by Cruding or runtime watcher, not public controller.' },
    @{ Source = 'config/routes/role_context.yaml'; Surface = 'front-runtime'; Reason = 'Front context route should be owned by Cruding.' },
    @{ Source = 'config/routes/role_eval.yaml'; Surface = 'front-runtime'; Reason = 'Front eval routes should be owned by Cruding.' },
    @{ Source = 'config/routes/role_explain.yaml'; Surface = 'front-runtime'; Reason = 'Front explain route should be owned by Cruding.' },
    @{ Source = 'config/routes/role_obligations.yaml'; Surface = 'front-runtime'; Reason = 'Front obligation routes should be owned by Cruding.' },
    @{ Source = 'config/routes/role_pdp.yaml'; Surface = 'front-runtime'; Reason = 'PDP routes should be explicit runtime service surface or Cruding-owned, not generic controller YAML.' },
    @{ Source = 'config/routes/role_pel.yaml'; Surface = 'front-runtime'; Reason = 'PEL route should be owned by Cruding.' },
    @{ Source = 'config/routes/role_perm_catalog.yaml'; Surface = 'front-runtime'; Reason = 'Permission catalog route should be owned by Cruding.' },
    @{ Source = 'config/routes/role_policy.yaml'; Surface = 'front-runtime'; Reason = 'Policy routes should be owned by Cruding or EA admin screen.' },
    @{ Source = 'config/routes/role_rebac.yaml'; Surface = 'front-runtime'; Reason = 'ReBAC routes should be owned by Cruding.' },
    @{ Source = 'config/routes/role_residency.yaml'; Surface = 'front-runtime'; Reason = 'Residency route should be owned by Cruding.' },
    @{ Source = 'config/routes/role_security.yaml'; Surface = 'front-runtime'; Reason = 'Security key routes should be explicit runtime surface or EA/admin action, not generic public route.' },
    @{ Source = 'config/routes/role_shadow.yaml'; Surface = 'debug-runtime'; Reason = 'Debug route should be dev-only or tool-owned.' },
    @{ Source = 'config/routes/role_sod.yaml'; Surface = 'front-runtime'; Reason = 'SOD route should be owned by Cruding.' },
    @{ Source = 'config/routes/role_tenants.yaml'; Surface = 'runtime-admin-json'; Reason = 'Tenant admin JSON routes are not native EasyAdmin. Convert before removing.' },
    @{ Source = 'config/routes/role_whatif.yaml'; Surface = 'front-runtime'; Reason = 'What-if route should be owned by Cruding.' },
    @{ Source = 'config/routes.yaml'; Surface = 'route-index'; Reason = 'Current route index imports public/runtime route YAML. Replace later with admin EA import only when EA controllers exist.' }
)

foreach ($item in $routeRows) {
    Add-PlanRow -Rows $rows `
        -Kind 'route' `
        -Source $item.Source `
        -Action 'delete-or-rewrite-after-surface-split' `
        -Target '' `
        -Surface $item.Surface `
        -Safety 'ReviewRequired' `
        -Reason $item.Reason
}

# Generated/runtime files are safe hygiene deletes.
$generatedRows = @(
    @{ Source = 'var/.php-cs-fixer.cache'; Reason = 'Generated local php-cs-fixer cache.' },
    @{ Source = 'var/cache'; Reason = 'Generated Symfony/PHPStan/runtime cache directory. Should not be versioned.' }
)

foreach ($item in $generatedRows) {
    $exists = Test-Path -LiteralPath (Join-RepoPath $item.Source)
    $rows.Add([pscustomobject]@{
        Kind   = 'generated-runtime'
        Source = $item.Source
        Action = 'delete'
        Target = ''
        Surface = 'hygiene'
        Safety = 'SafeToApply'
        Exists = $exists
        Reason = $item.Reason
    }) | Out-Null
}

# Target placeholder for native EA surface. This is a directory ensure, not a controller move.
$rows.Add([pscustomobject]@{
    Kind   = 'directory'
    Source = ''
    Action = 'ensure-directory'
    Target = 'src/Controller/Admin'
    Surface = 'admin-easyadmin'
    Safety = 'SafeToApply'
    Exists = (Test-Path -LiteralPath (Join-RepoPath 'src/Controller/Admin') -PathType Container)
    Reason = 'Canonical location for native EasyAdmin CRUD/Dashboard controllers.'
}) | Out-Null

$csvPath = Join-Path $OutputPath 'rolling-w02-controller-move-delete-plan.csv'
$mdPath = Join-Path $OutputPath 'rolling-w02-controller-move-delete-plan.md'
$rows | Export-Csv -LiteralPath $csvPath -NoTypeInformation -Encoding UTF8

$summary = $rows | Group-Object Kind, Safety | Sort-Object Name | ForEach-Object {
    "| $($_.Name) | $($_.Count) |"
}

$md = @()
$md += '# Rolling W02 Controller Move/Delete Plan'
$md += ''
$md += 'Canon: Rolling is 1+2 surface.'
$md += ''
$md += '- Admin surface: native EasyAdmin controllers are allowed only under `src/Controller/Admin/*`.'
$md += '- Front surface: zero public controllers; Cruding + Viewing + Interfacing own front request/view/template flow.'
$md += '- Existing `src/Controller/Api/*`, `src/Controller/V2/*`, health and metrics controllers are not moved blindly to Admin because they are JSON/runtime controllers, not native EasyAdmin CRUD/Dashboard classes.'
$md += ''
$md += '## Summary'
$md += ''
$md += '| Group | Count |'
$md += '|---|---:|'
$md += $summary
$md += ''
$md += '## Plan'
$md += ''
$md += '| Kind | Source | Action | Target | Surface | Safety | Exists | Reason |'
$md += '|---|---|---|---|---|---|---:|---|'
foreach ($r in $rows) {
    $md += "| $($r.Kind) | `$($r.Source)` | $($r.Action) | `$($r.Target)` | $($r.Surface) | $($r.Safety) | $($r.Exists) | $($r.Reason.Replace('|', '\|')) |"
}
$md += ''
$md += '## Apply policy'
$md += ''
$md += 'Default execution is read-only. Destructive changes require `-Apply`. Review-required rows require both `-Apply` and `-IncludeReviewRequired`. Route deletion additionally requires `-IncludeRoutes`.'
$md += ''
$md += 'Recommended first run:'
$md += ''
$md += '```powershell'
$md += '.\tools\w02\rolling-w02-controller-move-delete-plan.ps1 -RootPath .'
$md += '```'
$md += ''
$md += 'Safe dry-run apply for hygiene only:'
$md += ''
$md += '```powershell'
$md += '.\tools\w02\rolling-w02-controller-move-delete-plan.ps1 -RootPath . -Apply -WhatIf'
$md += '```'
$md += ''
$md += 'Full destructive dry-run after review:'
$md += ''
$md += '```powershell'
$md += '.\tools\w02\rolling-w02-controller-move-delete-plan.ps1 -RootPath . -Apply -IncludeReviewRequired -IncludeRoutes -WhatIf'
$md += '```'
Set-Content -LiteralPath $mdPath -Value $md -Encoding UTF8

Write-Host "Rolling W02 plan written:" -ForegroundColor Green
Write-Host "  $csvPath"
Write-Host "  $mdPath"
Write-Host ''
Write-Host 'Summary:' -ForegroundColor Cyan
$rows | Group-Object Kind, Safety | Sort-Object Name | ForEach-Object {
    Write-Host ("  {0}: {1}" -f $_.Name, $_.Count)
}

if (-not $Apply) {
    Write-Host ''
    Write-Host 'Read-only mode. Nothing changed.' -ForegroundColor Yellow
    exit 0
}

foreach ($r in $rows) {
    if ($r.Safety -eq 'ReviewRequired' -and -not $IncludeReviewRequired) {
        continue
    }
    if ($r.Kind -eq 'route' -and -not $IncludeRoutes) {
        continue
    }

    if ($r.Action -eq 'ensure-directory') {
        $targetPath = Join-RepoPath $r.Target
        if ($PSCmdlet.ShouldProcess($targetPath, 'Ensure directory')) {
            New-Item -ItemType Directory -Force -Path $targetPath | Out-Null
        }
        continue
    }

    if ($r.Action -like 'delete*') {
        $sourcePath = Join-RepoPath $r.Source
        if (Test-Path -LiteralPath $sourcePath) {
            if ($PSCmdlet.ShouldProcess($sourcePath, 'Delete')) {
                Remove-Item -LiteralPath $sourcePath -Recurse -Force
            }
        }
    }
}
