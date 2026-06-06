param(
    [string]$RootPath = ".",
    [string]$OutDir = "var/audit/symfony81",
    [switch]$OpenReport
)

$ErrorActionPreference = "Stop"
$root = (Resolve-Path $RootPath).Path
$out = Join-Path $root $OutDir
New-Item -ItemType Directory -Force -Path $out | Out-Null

$php = Join-Path $root "tools/qa/rolling-symfony81-audit.php"
if (-not (Test-Path $php)) {
    throw "Missing tools/qa/rolling-symfony81-audit.php"
}

$jsonPath = Join-Path $out "rolling-symfony81-audit.json"
$mdPath = Join-Path $out "rolling-symfony81-audit.md"

$raw = & php $php
$exitCode = $LASTEXITCODE
$raw | Set-Content -Path $jsonPath -Encoding UTF8
$data = $raw | ConvertFrom-Json

$lines = New-Object System.Collections.Generic.List[string]
$lines.Add("# Rolling Symfony 8.1 readiness audit")
$lines.Add("")
$lines.Add("Root: ``$root``")
$lines.Add("Generated: $((Get-Date).ToString('yyyy-MM-dd HH:mm:ss'))")
$lines.Add("")
$lines.Add("## Summary")
$lines.Add("")
$lines.Add("- PHP files scanned: $($data.summary.php_files_scanned)")
$lines.Add("- Text files scanned: $($data.summary.text_files_scanned)")
$lines.Add("- Blockers: $($data.summary.blocker_count)")
$lines.Add("- Review candidates: $($data.summary.review_count)")
$lines.Add("")

foreach ($check in $data.checks.PSObject.Properties) {
    $items = @($check.Value)
    $lines.Add("## $($check.Name) ($($items.Count))")
    $lines.Add("")
    if ($items.Count -eq 0) {
        $lines.Add("No hits.")
        $lines.Add("")
        continue
    }

    foreach ($item in $items | Select-Object -First 80) {
        $lines.Add("- [$($item.severity)] ``$($item.file):$($item.line)`` — $($item.note)")
        $lines.Add("  - ``$($item.match)``")
    }
    if ($items.Count -gt 80) {
        $lines.Add("- ... truncated in markdown; see JSON for full details.")
    }
    $lines.Add("")
}

$lines | Set-Content -Path $mdPath -Encoding UTF8

Write-Host "JSON: $jsonPath"
Write-Host "MD:   $mdPath"
Write-Host "Exit: $exitCode"

if ($OpenReport) {
    Invoke-Item $mdPath
}

exit $exitCode
