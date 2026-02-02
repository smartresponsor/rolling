Param()
$ErrorActionPreference = "Stop"
$root = (Get-Location).Path
$reportDir = Join-Path $root "report"
New-Item -ItemType Directory -Path $reportDir -Force | Out-Null
$txt = Join-Path $reportDir "sdk_sanity.txt"
"== SDK sanity == $( Get-Date ).ToUniversalTime()" | Out-File $txt -Encoding UTF8

$phpOk = $false
$tsOk = $false

"[PHP] running..." | Out-File $txt -Append -Encoding UTF8
try
{
    php examples/php/check.php | Tee-Object -FilePath (Join-Path $reportDir "php_sdk.json")
    $phpOk = (Select-String -Path (Join-Path $reportDir "php_sdk.json") -Pattern '"decision"' -Quiet)
    if ($phpOk)
    {
        "[PHP] PASS" | Out-File $txt -Append -Encoding UTF8
    }
    else
    {
        "[PHP] FAIL" | Out-File $txt -Append -Encoding UTF8
    }
}
catch
{
    "[PHP] FAIL: $( $_.Exception.Message )" | Out-File $txt -Append -Encoding UTF8
}

if (Get-Command node -ErrorAction SilentlyContinue)
{
    "[TS] running..." | Out-File $txt -Append -Encoding UTF8
    Push-Location examples/js
    npm i | Out-Null
    try
    {
        node --loader ts-node/esm ../js/check.ts | Tee-Object -FilePath (Join-Path $reportDir "ts_sdk.json")
        $tsOk = (Select-String -Path (Join-Path $reportDir "ts_sdk.json") -Pattern '"decision"' -Quiet)
        if ($tsOk)
        {
            "[TS] PASS" | Out-File $txt -Append -Encoding UTF8
        }
        else
        {
            "[TS] FAIL" | Out-File $txt -Append -Encoding UTF8
        }
    }
    catch
    {
        "[TS] FAIL: $( $_.Exception.Message )" | Out-File $txt -Append -Encoding UTF8
    }
    Pop-Location
}
else
{
    "[TS] skipped — node not found" | Out-File $txt -Append -Encoding UTF8
}

if ($phpOk -and $tsOk)
{
    "ACCEPT: true" | Out-File $txt -Append -Encoding UTF8; exit 0
}
else
{
    "ACCEPT: false" | Out-File $txt -Append -Encoding UTF8; exit 1
}
