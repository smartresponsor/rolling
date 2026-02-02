# Smoke for Role Step 36 (Retention/Housekeeping)
param([string]$Php = "php")
Write-Host "PHP version:"; & $Php -v
$files = @(
"src/Housekeeping/Role/Clock.php",
"src/Housekeeping/Role/PdoAuditGc.php",
"src/Housekeeping/Role/PdoReplayGc.php",
"src/Housekeeping/Role/Archive/JsonlAuditArchiver.php",
"src/Housekeeping/Role/Janitor.php",
"bin/role-janitor.php",
"ops/retention.json",
"ops/systemd/role-janitor.service",
"ops/systemd/role-janitor.timer",
"ops/README.md",
"tests/Role/Housekeeping/AuditGcTest.php",
"tests/Role/Housekeeping/ReplayGcTest.php",
"tests/Role/Housekeeping/ArchiveTest.php",
"MANIFEST.md"
)
$errors = 0
foreach ($f in $files)
{
    if ($f -like "*.php")
    {
        & $Php -l $f | Write-Host; if ($LASTEXITCODE -ne 0)
        {
            $errors++
        }
    }
    else
    {
        Write-Host "OK $f"
    }
}
if ($errors -gt 0)
{
    Write-Error "Smoke failed with $errors error(s)."; exit 1
}
Write-Host "`nSmoke OK."
