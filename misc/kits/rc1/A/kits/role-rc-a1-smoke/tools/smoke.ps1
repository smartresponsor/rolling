Param(
    [string]$PhpPath = "php"
)
$ErrorActionPreference = "Stop"
$root = (Get-Location).Path
$reportDir = Join-Path $root "report"
New-Item -ItemType Directory -Force -Path $reportDir | Out-Null
$txt = Join-Path $reportDir "smoke.txt"
$json = Join-Path $reportDir "smoke.json"

"== RC-A1 Smoke == $( Get-Date ).ToUniversalTime()" | Out-File $txt -Encoding UTF8

$php = Get-Command $PhpPath -ErrorAction SilentlyContinue
if (-not$php)
{
    "PHP not found in PATH" | Out-File $txt -Append -Encoding UTF8
    '{"ok":false,"reason":"php-not-found"}' | Out-File $json -Encoding UTF8
    exit 1
}

(& $PhpPath -v)[0] | ForEach-Object { "PHP: $_" } | Out-File $txt -Append -Encoding UTF8
"" | Out-File $txt -Append -Encoding UTF8

$paths = @("src", "sdk/php", "tests", "bin")
$files = foreach ($p in $paths)
{
    if (Test-Path $p)
    {
        Get-ChildItem -Path $p -Recurse -Filter *.php | ForEach-Object { $_.FullName }
    }
}
$files = $files | Sort-Object
$total = $files.Count
"[Lint] Files to check: $total" | Out-File $txt -Append -Encoding UTF8

$errors = 0
foreach ($f in $files)
{
    $out = & $PhpPath -l $f 2>&1
    if ($LASTEXITCODE -ne 0 -or $out -match "Errors parsing")
    {
        "E: $out" | Out-File $txt -Append -Encoding UTF8
        $errors++
    }
}
"[Lint] Syntax errors: $errors" | Out-File $txt -Append -Encoding UTF8

"" | Out-File $txt -Append -Encoding UTF8
$testsRun = $false
$testsOk = $null
$testsNote = "not-run"

$phpunit = $null
if (Test-Path "vendor/bin/phpunit")
{
    $phpunit = "vendor/bin/phpunit"
}
elseif (Get-Command phpunit -ErrorAction SilentlyContinue)
{
    $phpunit = "phpunit"
}

if ($phpunit)
{
    "Running tests: $phpunit --colors=never" | Out-File $txt -Append -Encoding UTF8
    & $phpunit --colors= never | Tee-Object -FilePath $txt -Append
    $code = $LASTEXITCODE
    $testsRun = $true
    $testsOk = ($code -eq 0)
    $testsNote = "exit:$code"
}
else
{
    "PHPUnit not found — tests skipped (mark @skip with reason if needed)" | Out-File $txt -Append -Encoding UTF8
    $testsRun = $false
    $testsOk = $null
    $testsNote = "phpunit-not-found"
}

$acceptLint = ($errors -eq 0)
if ($testsRun)
{
    $acceptTests = ($testsOk -eq $true)
}
else
{
    $acceptTests = $true
}
$accept = ($acceptLint -and $acceptTests)

"" | Out-File $txt -Append -Encoding UTF8
"== Summary ==" | Out-File $txt -Append -Encoding UTF8
"lint_ok: $acceptLint" | Out-File $txt -Append -Encoding UTF8
"tests_run: $testsRun" | Out-File $txt -Append -Encoding UTF8
"tests_ok: $testsOk" | Out-File $txt -Append -Encoding UTF8
"tests_note: $testsNote" | Out-File $txt -Append -Encoding UTF8
"accept: $accept" | Out-File $txt -Append -Encoding UTF8

$obj = @{
    lint = @{ files = $total; errors = $errors; ok = $acceptLint }
    tests = @{ run = $testsRun; ok = $testsOk; note = $testsNote }
    accept = $accept
    timestamp = (Get-Date).ToUniversalTime().ToString("o")
} | ConvertTo-Json -Depth 4
$obj | Out-File $json -Encoding UTF8

"Report: $txt"
