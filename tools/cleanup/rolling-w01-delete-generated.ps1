param(
    [string] $RootPath = (Get-Location).Path,
    [switch] $WhatIf
)

$ErrorActionPreference = 'Stop'

$targets = @(
    'var/cache',
    'var/phpstan',
    'var/.php-cs-fixer.cache',
    '.phpunit.result.cache',
    '.phpunit.cache'
)

foreach ($relativePath in $targets) {
    $absolutePath = Join-Path $RootPath $relativePath

    if (-not (Test-Path -LiteralPath $absolutePath)) {
        Write-Host "skip: $relativePath"
        continue
    }

    if ($WhatIf) {
        Write-Host "would delete: $relativePath"
        continue
    }

    Remove-Item -LiteralPath $absolutePath -Recurse -Force
    Write-Host "deleted: $relativePath"
}
