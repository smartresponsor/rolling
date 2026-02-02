# Obligations: Masking/Redaction

- Interface: `ServiceInterface/Role/Policy/Obligation/ObligationApplierInterface`
- Implementation: `Service/Role/Policy/Obligation/Masking/MaskingEngine`
- Rule repository: `InfraInterface/Role/Policy/MaskingRuleRepositoryInterface` + InMemory implementation
- Rule format (NDJSON): see `examples/masking_rules.ndjson`

## Quick start

```
php tools/obligation/mask_demo.php
cat report/mask_demo.json
```

## Notes

- EN-only comments; single-hyphen naming; layer-first isolation; no stubs/TODO.
- Extend with DB/secret-store backed repository and field-level codecs if needed.
