Param()
# Go
Push-Location sdk/go
go build ./...
Pop-Location
# Java
Push-Location sdk/java
Get-ChildItem -Recurse src/main/java -Filter *.java | ForEach-Object FullName > sources.txt
javac @sources.txt
Pop-Location
Write-Host "OK build Go+Java SDKs"
