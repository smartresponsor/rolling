# Security

- Keep `var/admin_secret.txt` secret; rotate keys via `/v2/admin/keys/rotate`.
- Sign import bundles; verify with `BundleVerifier`.
- Consider mTLS between adapters and core.
- Limit admin endpoints behind network policies and real authN (OIDC).