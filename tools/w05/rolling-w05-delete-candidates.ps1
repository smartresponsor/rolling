<#
.SYNOPSIS
  Rolling W05 delete candidates scanner / optional safe cleanup.

.DESCRIPTION
  Scans a Rolling repository for files/directories that are safe or review-required delete candidates
  after the 1+2 surface cleanup:
    - generated runtime/cache files
    - obsolete generic Symfony controllers outside src/Controller/Admin
    - empty directories left after controller evacuation
    - local patch/report artifacts accidentally copied into the repository

  Default mode is read-only. Use -Apply to delete safe candidates. Use -IncludeReviewRequired
  only after reading the generated reports. -WhatIf is supported.

.EXAMPLE
  .\tools\w05\rolling-w05-delete-candidates.ps1 -RootPath .

.EXAMPLE
  .\tools\w05\rolling-w05-delete-candidates.ps1 -RootPath . -Apply -WhatIf

.EXAMPLE
  .\tools\w05\rolling-w05-delete-candidates.ps1 -RootPath . -Apply -IncludeReviewRequired -WhatIf
#>

[CmdletBinding(SupportsShouldProcess = $true)]
param(
    [Parameter(Mandatory = $false)]
    [string]$RootPath = '.',

    [Parameter(Mandatory = $false)]
    [string]$OutDir = 'var/w05-delete-plan',

    [Parameter(Mandatory = $false)]
    [switch]$Apply,

    [Parameter(Mandatory = $false)]
    [switch]$IncludeReviewRequired
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

function Resolve-FullPath([string]$Path) {
    return [System.IO.Path]::GetFullPath((Join-Path (Get-Location) $Path))
}

function Get-RelativePathSafe([string]$BasePath, [string]$TargetPath) {
    $base = [System.IO.Path]::GetFullPath($BasePath).TrimEnd([System.IO.Path]::DirectorySeparatorChar, [System.IO.Path]::AltDirectorySeparatorChar) + [System.IO.Path]::DirectorySeparatorChar
    $target = [System.IO.Path]::GetFullPath($TargetPath)
    $baseUri = [Uri]::new($base)
    $targetUri = [Uri]::new($target)
    return [Uri]::UnescapeDataString($baseUri.MakeRelativeUri($targetUri).ToString()).Replace('/', [System.IO.Path]::DirectorySeparatorChar)
}

function Add-Candidate {
    param(
        [System.Collections.Generic.List[object]]$Bag,
        [string]$Path,
        [string]$Kind,
        [string]$Bucket,
        [string]$Safety,
        [string]$Reason
    )

    if (-not (Test-Path -LiteralPath $Path)) {
        return
    }

    $item = Get-Item -LiteralPath $Path -Force
    $Bag.Add([pscustomobject]@{
        Path = Get-RelativePathSafe -BasePath $script:RootFullPath -TargetPath $item.FullName
        FullPath = $item.FullName
        Kind = $Kind
        Bucket = $Bucket
        Safety = $Safety
        Reason = $Reason
    }) | Out-Null
}

$script:RootFullPath = Resolve-FullPath $RootPath
if (-not (Test-Path -LiteralPath $script:RootFullPath -PathType Container)) {
    throw "RootPath is not a directory: $RootPath"
}

$OutFullPath = Join-Path $script:RootFullPath $OutDir
New-Item -ItemType Directory -Force -Path $OutFullPath | Out-Null

$candidates = [System.Collections.Generic.List[object]]::new()

# Safe generated/runtime files and directories.
$safeGenerated = @(
    'var/cache',
    'var/log',
    'var/.php-cs-fixer.cache',
    '.php-cs-fixer.cache',
    '.phpunit.result.cache',
    '.phpunit.cache',
    '.phpstan',
    '.psalm/cache',
    'coverage',
    'build/coverage',
    'tmp',
    'temp'
)

foreach ($rel in $safeGenerated) {
    $path = Join-Path $script:RootFullPath $rel
    if (Test-Path -LiteralPath $path) {
        $kind = if ((Get-Item -LiteralPath $path -Force).PSIsContainer) { 'directory' } else { 'file' }
        Add-Candidate $candidates $path $kind 'generated-runtime' 'safe' 'Generated runtime/cache artifact; should not be committed into Rolling source snapshots.'
    }
}

# Obsolete controller layer after W02: only src/Controller/Admin is allowed.
$controllerRoot = Join-Path $script:RootFullPath 'src/Controller'
if (Test-Path -LiteralPath $controllerRoot -PathType Container) {
    Get-ChildItem -LiteralPath $controllerRoot -Recurse -File -Filter '*.php' | ForEach-Object {
        $rel = Get-RelativePathSafe -BasePath $script:RootFullPath -TargetPath $_.FullName
        $normalized = $rel.Replace('\\', '/')
        if ($normalized -notlike 'src/Controller/Admin/*') {
            Add-Candidate $candidates $_.FullName 'file' 'obsolete-controller' 'review-required' 'Generic/public/API controller outside src/Controller/Admin; Rolling front surface must be zero-controller.'
        }
    }
}

# Route files that still point to Controller namespaces are review-required delete/update candidates.
$routesRoot = Join-Path $script:RootFullPath 'config/routes'
if (Test-Path -LiteralPath $routesRoot -PathType Container) {
    Get-ChildItem -LiteralPath $routesRoot -Recurse -File -Include '*.yaml','*.yml','*.php','*.xml' | ForEach-Object {
        $content = Get-Content -LiteralPath $_.FullName -Raw -ErrorAction SilentlyContinue
        if ($content -match 'App\\Rolling\\Controller\\' -or $content -match 'src/Controller' -or $content -match 'Controller\\Api\\' -or $content -match 'Controller\\V2\\') {
            Add-Candidate $candidates $_.FullName 'file' 'obsolete-route-controller-reference' 'review-required' 'Route file still references obsolete controller namespace/path.'
        }
    }
}

# Patch/report bundles copied into repo by accident.
$artifactPatterns = @(
    '*-patchkit.zip',
    '*-readiness.patch',
    '*-http-transform.patch',
    '*-payload-dto.patch',
    '*-audit.json',
    '*-report.md',
    'Rolling-*-w*.zip'
)

foreach ($pattern in $artifactPatterns) {
    Get-ChildItem -LiteralPath $script:RootFullPath -File -Filter $pattern -ErrorAction SilentlyContinue | ForEach-Object {
        Add-Candidate $candidates $_.FullName 'file' 'local-patch-artifact' 'review-required' 'Local generated patch/report/archive artifact; keep outside repo unless intentionally documented.'
    }
}

# Empty dirs after evacuation. Keep common structural dirs if intentionally empty only after review.
Get-ChildItem -LiteralPath $script:RootFullPath -Directory -Recurse -Force |
    Sort-Object FullName -Descending |
    ForEach-Object {
        $children = Get-ChildItem -LiteralPath $_.FullName -Force -ErrorAction SilentlyContinue
        if (($children | Measure-Object).Count -eq 0) {
            $rel = (Get-RelativePathSafe -BasePath $script:RootFullPath -TargetPath $_.FullName).Replace('\\','/')
            if ($rel -notlike '.git/*' -and $rel -ne '.git') {
                Add-Candidate $candidates $_.FullName 'directory' 'empty-directory' 'review-required' 'Empty directory left after cleanup; delete if no placeholder policy requires it.'
            }
        }
    }

# De-duplicate by FullPath.
$candidates = [System.Collections.Generic.List[object]]::new(($candidates | Sort-Object FullPath -Unique))

$csvPath = Join-Path $OutFullPath 'rolling-w05-delete-candidates.csv'
$jsonPath = Join-Path $OutFullPath 'rolling-w05-delete-candidates.json'
$mdPath = Join-Path $OutFullPath 'rolling-w05-delete-candidates.md'

$candidates | Select-Object Path,Kind,Bucket,Safety,Reason | Export-Csv -NoTypeInformation -Encoding UTF8 -Path $csvPath
$candidates | Select-Object Path,Kind,Bucket,Safety,Reason | ConvertTo-Json -Depth 4 | Set-Content -Encoding UTF8 -Path $jsonPath

$safeCount = ($candidates | Where-Object { $_.Safety -eq 'safe' } | Measure-Object).Count
$reviewCount = ($candidates | Where-Object { $_.Safety -eq 'review-required' } | Measure-Object).Count

$md = [System.Collections.Generic.List[string]]::new()
$md.Add('# Rolling W05 delete candidates') | Out-Null
$md.Add('') | Out-Null
$md.Add("Root: ``$script:RootFullPath``") | Out-Null
$md.Add('') | Out-Null
$md.Add("Safe candidates: **$safeCount**") | Out-Null
$md.Add("Review-required candidates: **$reviewCount**") | Out-Null
$md.Add('') | Out-Null
$md.Add('## Canon') | Out-Null
$md.Add('') | Out-Null
$md.Add('- `src/Controller/Admin/*` is allowed for native EasyAdmin only.') | Out-Null
$md.Add('- Generic/public/API controllers outside `src/Controller/Admin` are delete or transform candidates.') | Out-Null
$md.Add('- Generated runtime/cache artifacts must not be committed into Rolling source snapshots.') | Out-Null
$md.Add('') | Out-Null
$md.Add('## Candidates') | Out-Null
$md.Add('') | Out-Null
$md.Add('| Safety | Bucket | Kind | Path | Reason |') | Out-Null
$md.Add('|---|---|---|---|---|') | Out-Null
foreach ($c in ($candidates | Sort-Object Safety, Bucket, Path)) {
    $pathEscaped = $c.Path.Replace('|', '\|')
    $reasonEscaped = $c.Reason.Replace('|', '\|')
    $md.Add("| $($c.Safety) | $($c.Bucket) | $($c.Kind) | ``$pathEscaped`` | $reasonEscaped |") | Out-Null
}
$md | Set-Content -Encoding UTF8 -Path $mdPath

Write-Host "Rolling W05 delete scan complete." -ForegroundColor Green
Write-Host "Safe candidates: $safeCount"
Write-Host "Review-required candidates: $reviewCount"
Write-Host "CSV:  $csvPath"
Write-Host "JSON: $jsonPath"
Write-Host "MD:   $mdPath"

if ($Apply) {
    $toDelete = if ($IncludeReviewRequired) {
        $candidates
    } else {
        $candidates | Where-Object { $_.Safety -eq 'safe' }
    }

    foreach ($candidate in ($toDelete | Sort-Object { $_.FullPath.Length } -Descending)) {
        if (-not (Test-Path -LiteralPath $candidate.FullPath)) {
            continue
        }

        $target = "$($candidate.Kind): $($candidate.Path)"
        if ($PSCmdlet.ShouldProcess($target, 'Delete')) {
            Remove-Item -LiteralPath $candidate.FullPath -Recurse -Force
        }
    }
}
