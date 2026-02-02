# Keys & JWKS (RC5 E10)

- HMAC store: `var/keys/<tenant>/hmac/current.key` + `archive/*.key` (JSON: {kid,key})
- Rotate: `POST /v2/keys/rotate` -> returns new kid (archives previous)
- Sign HS256: `POST /v2/keys/sign` (claims) -> `jwt`
- Verify HS256/RS256: `POST /v2/keys/verify` (token) -> `ok, header, payload, kid`
- JWKS: `GET /v2/keys/jwks?tenant=t1`, `POST /v2/keys/jwks` to replace

Security notes:

- Dev PEM in `var/keys/t1/jwks.json` is for demo only.
- Never commit real keys. Use KMS for production (AWS KMS hooks planned in RC5 addons).
  Generated: 2025-10-27T18:11:38Z UTC
