#!/usr/bin/env bash
set -euo pipefail

cd /app

if [ ! -f vendor/autoload.php ]; then
  composer install --prefer-dist --no-interaction
fi

if [ ! -d node_modules ]; then
  npm install
fi

exec "$@"
