#!/usr/bin/env bash
set -euo pipefail
KIT_DIR="${1:-}"
if [[ -z "$KIT_DIR" || ! -d "$KIT_DIR" ]]; then
  echo "Usage: $0 <kit_dir>"; exit 1
fi

copy_if_exists() {
  local src="$1"; local dst="$2";
  if [[ -d "$src" ]]; then
    mkdir -p "$dst"
    rsync -a "$src"/ "$dst"/
  fi
}

# Known folders to merge
copy_if_exists "$KIT_DIR/src" "./src"
copy_if_exists "$KIT_DIR/tests" "./tests"
copy_if_exists "$KIT_DIR/sdk" "./sdk"
copy_if_exists "$KIT_DIR/bin" "./bin"
copy_if_exists "$KIT_DIR/tools" "./tools"
copy_if_exists "$KIT_DIR/ops" "./ops"
copy_if_exists "$KIT_DIR/docs" "./docs"

echo "Integrated from $KIT_DIR"
