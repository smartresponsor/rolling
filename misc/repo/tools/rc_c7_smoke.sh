#!/usr/bin/env bash
set -euo pipefail
# Go
pushd sdk/go >/dev/null
go build ./...
popd >/dev/null

# Java
pushd sdk/java >/dev/null
find src/main/java -name '*.java' > sources.txt
javac @sources.txt
popd >/dev/null
echo "OK build Go+Java SDKs"
