# Data residency & keys (G8)

Goal: tenant-scoped data residency and key management for signing and encryption.

Components:

- `StaticResidencyPolicy` implements `ResidencyPolicyInterface` → map tenant→region.
- `ResidencyStorage` writes blobs to `var/residency/<region>/<tenant>/<kind>/<name>`.
- `FileKeyProvider` implements `KeyProviderInterface` with `rotate/getActive/getById`.
- `HmacSigner` (HMAC-SHA256) produces `{kid,sig}`; `verify()` checks by `kid`.
- `SimpleEncryptor` wraps AES-256-GCM (openssl), returns `{kid,iv,ct,tag}`.

Demo:

```
php tools/residency/write_demo.php t1 policy policy_v1.php
php tools/key/sign.php t1 "payload" | jq .
php tools/key/verify.php t1 "payload" <kid> <sig>
php tools/key/rotate.php t1 | jq .
php tools/crypto/encrypt_demo.php t1 "hello"
```

Notes:

- Class/method names are singular.
- Comments are EN-only; naming uses single-hyphen on files.
