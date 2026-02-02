Param(
    [Parameter(Mandatory = $true)]
    [string]$KitDir
)
if (!(Test-Path $KitDir))
{
    Write-Error "Kit dir not found"; exit 1
}

function Copy-IfExists($src, $dst)
{
    if (Test-Path $src)
    {
        New-Item -ItemType Directory -Force -Path $dst | Out-Null
        robocopy $src $dst /E /NFL /NDL /NJH /NJS /NP | Out-Null
    }
}

Copy-IfExists "$KitDir/src" "./src"
Copy-IfExists "$KitDir/tests" "./tests"
Copy-IfExists "$KitDir/sdk" "./sdk"
Copy-IfExists "$KitDir/bin" "./bin"
Copy-IfExists "$KitDir/tools" "./tools"
Copy-IfExists "$KitDir/ops" "./ops"
Copy-IfExists "$KitDir/docs" "./docs"

Write-Host "Integrated from $KitDir"
