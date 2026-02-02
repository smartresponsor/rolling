# Install & Run (RC4)

## Prereqs

- PHP 8.2+, ext-json
- Node 18+ (for TS SDK example), Go 1.22+, Java 11+ (optional)
- Docker (optional) for Envoy/Oathkeeper demo

## Quick start

```bash
# unzip and smoke
unzip role-rc4-src.zip
cd repo
bash tools/rc4_pre_total_smoke.sh
bash tools/rc_d10_smoke.sh

# run your Symfony app with added routes/config (see docs/*)
# /v2/check available; debug UI: public/role/debug/check.html
```
