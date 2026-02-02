# Tenant Keys Isolation

- Interfaces in `InfraInterface/Role/Tenant` and `ServiceInterface/Role/Tenant`.
- InMemory repo + provider generate base64url keys per tenant.
- HMAC signer (Base64Url) for component-to-component signatures.

## Tools

```
php tools/tenant/keygen.php     # → report/tenant_keys.json for examples/tenant_list.ndjson
php tools/tenant/sign_verify.php# → report/sign_verify.json (cross-tenant must be false)
```

## Notes

- EN-only comments; single-hyphen naming; layer-first isolation (interfaces vs implementations).
- Replace InMemory repo with DB/secret-manager in production.
