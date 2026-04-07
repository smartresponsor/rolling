# Rolling / Role w22 CLI migration parity

This wave continues native Symfony Console migration for the remaining operational flows from `bin/role-policy.php`, `bin/role-admin.php`, and `bin/role-janitor.php`.

Added parity commands:
- policy import / activate / export / migrate
- admin policy import / activate / export
- janitor gc-audit / gc-replay / archive-audit

The native Console layer now covers the main operational surface while preserving the existing legacy scripts as continuity shims.
