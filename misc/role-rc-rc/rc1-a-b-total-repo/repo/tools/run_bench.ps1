Param()
$ErrorActionPreference = "Stop"
chmod +x bin/role-bench.php 2> $null | Out-Null
php bin/role-bench.php | Tee-Object -FilePath (Join-Path "report/bench" "run_stdout.txt")
Write-Host "Done."
