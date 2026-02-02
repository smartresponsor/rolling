Param([string]$Message = "role: apply changes")
git add -A
try
{
    git commit -m $Message
}
catch
{
    Write-Host "No changes to commit"
}
