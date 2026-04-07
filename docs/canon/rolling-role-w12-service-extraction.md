# Rolling / Role — w12 service extraction

This wave extracts the winning application-service slice from `src/Legacy/Service*` into canonical Symfony-oriented `src/Service*` and `src/ServiceInterface*` under the single `App\` namespace root.

Extracted slices:
- PDP batch and DTO/contracts
- Policy engine / voters / compiler
- Obligation runner / applier
- ReBAC checker / writer / namespace constraint

Legacy classes remain as donor and BC bridge through `legacy_role_w12_aliases.php`.
