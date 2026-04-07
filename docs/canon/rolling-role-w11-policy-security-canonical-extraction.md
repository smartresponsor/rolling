# Rolling / Role w11

This wave starts real extraction of winning execution-layer code from `src/Legacy` into canonical Symfony-oriented layers under `src/Policy`, `src/PolicyInterface`, and `src/Security`.

Added canonical classes:
- `src/PolicyInterface/PdpV2Interface.php`
- `src/Policy/Batch/CheckBatchProcessor.php`
- `src/Policy/Obligation/Obligation.php`
- `src/Policy/Obligation/Obligations.php`
- `src/Policy/Opa/InputBuilder.php`
- `src/Policy/Opa/OpaPdpV2.php`
- `src/Policy/V2/DecisionWithObligations.php`
- `src/Policy/Client/V2/RemotePdpV2.php`
- `src/Policy/Decorator/V2/AuditingPdp.php`
- `src/Policy/Decorator/V2/CachedPdpV2.php`
- `src/Policy/Decorator/V2/RegistryBackedPdp.php`
- `src/Security/Util/Base64Url.php`
- `src/Security/Hmac/Canonicalizer.php`
- `src/Security/Hmac/Signer.php`
- `src/Security/Hmac/Verifier.php`

Compatibility bridge added:
- `src/Legacy/Compatibility/legacy_role_w11_aliases.php`

Legacy remains as donor / backward-compatibility zone in this wave.
