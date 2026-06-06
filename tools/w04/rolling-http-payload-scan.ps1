param(
    [string]$RootPath = ".",
    [string]$OutputDirectory = "report/w04"
)

$ErrorActionPreference = "Stop"
$root = (Resolve-Path $RootPath).Path
$http = Join-Path $root "src/Service/Http"
$outDir = Join-Path $root $OutputDirectory
New-Item -ItemType Directory -Force -Path $outDir | Out-Null

$rows = @()
Get-ChildItem -Path $http -Recurse -File -Filter "*.php" | ForEach-Object {
    $content = Get-Content -LiteralPath $_.FullName -Raw
    $rel = $_.FullName.Substring($root.Length).TrimStart('\\','/') -replace '\\','/'
    $rows += [pscustomobject]@{
        File = $rel
        ManualGetContentJsonDecode = ([regex]::Matches($content, 'json_decode\s*\(.*?getContent\s*\(', 'Singleline')).Count
        UploadCandidate = [bool]($content -match '->files\b|UploadedFile')
        CheckboxNullCandidate = [bool]($content -match 'checkbox|\(bool\)|\bbool\b|not submitted|null|false')
        UsesJsonPayloadReader = [bool]($content -match 'JsonPayloadReader')
        UsesHttpPayloadDto = [bool]($content -match 'DTO\\Http\\Role')
    }
}

$csv = Join-Path $outDir "rolling-http-payload-scan.csv"
$md = Join-Path $outDir "rolling-http-payload-scan.md"
$rows | Export-Csv -NoTypeInformation -Encoding UTF8 -Path $csv

$manual = ($rows | Measure-Object -Property ManualGetContentJsonDecode -Sum).Sum
$uploads = @($rows | Where-Object { $_.UploadCandidate }).Count
$checkbox = @($rows | Where-Object { $_.CheckboxNullCandidate }).Count
$reader = @($rows | Where-Object { $_.UsesJsonPayloadReader }).Count
$dto = @($rows | Where-Object { $_.UsesHttpPayloadDto }).Count

$lines = @()
$lines += "# Rolling W04 HTTP payload scan"
$lines += ""
$lines += "- Manual getContent/json_decode count: $manual"
$lines += "- Upload candidates: $uploads"
$lines += "- Checkbox/null candidates: $checkbox"
$lines += "- Files using JsonPayloadReader: $reader"
$lines += "- Files using HTTP payload DTO: $dto"
$lines += ""
$lines += "## Files"
$lines += ""
$lines += "| File | Manual JSON | Upload | Checkbox/null | Reader | DTO |"
$lines += "|---|---:|---:|---:|---:|---:|"
foreach ($row in $rows) {
    $lines += "| $($row.File) | $($row.ManualGetContentJsonDecode) | $($row.UploadCandidate) | $($row.CheckboxNullCandidate) | $($row.UsesJsonPayloadReader) | $($row.UsesHttpPayloadDto) |"
}
Set-Content -LiteralPath $md -Encoding UTF8 -Value $lines

Write-Host "Wrote $csv"
Write-Host "Wrote $md"
