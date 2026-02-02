#!/usr/bin/env bash
set -euo pipefail
if [ $# -lt 5 ]; then
  echo "Usage: $0 BASE_URL METHOD PATH BODY_JSON SECRET"
  exit 2
fi
BASE="$1"; METHOD="$2"; PATHP="$3"; BODY="$4"; SECRET="$5"
DATE="$(LC_ALL=C date -u '+%a, %d %b %Y %H:%M:%S GMT')"
NONCE="$(uuidgen || cat /proc/sys/kernel/random/uuid || echo "nonce-$(date +%s)")"
SIG_BASE="$(printf '%s %s\n%s\n%s' "$(echo "$METHOD" | tr '[:lower:]' '[:upper:]')" "$PATHP" "$DATE" "$BODY")"
SIG="v1=$(printf '%s' "$SIG_BASE" | openssl dgst -sha256 -mac HMAC -macopt "key:$SECRET" -binary | base64)"

echo "== First request (should pass) =="
curl -i -s -X "$METHOD" "$BASE$PATHP" \
  -H "Content-Type: application/json" \
  -H "Date: $DATE" \
  -H "X-Request-Nonce: $NONCE" \
  -H "X-Signature: $SIG" \
  -d "$BODY" | sed -n '1,15p'

echo
echo "== Replay request (should 401 / replay) =="
curl -i -s -X "$METHOD" "$BASE$PATHP" \
  -H "Content-Type: application/json" \
  -H "Date: $DATE" \
  -H "X-Request-Nonce: $NONCE" \
  -H "X-Signature: $SIG" \
  -d "$BODY" | sed -n '1,15p'
