# Rolling / Role w18

- Extracted canonical security/key/residency/obligation infrastructure and services into `src/Infrastructure/*`, `src/InfrastructureInterface/*`, `src/Service/*`, and `src/ServiceInterface/*`.
- Switched primary controller/tool entrypoints away from legacy classes to canonical `App\Rolling\...` classes.
- Added `src/Legacy/Compatibility/legacy_role_w18_aliases.php` as BC bridge.
- Remaining structural tail by canon: `src/Legacy/Entity/Role`.
