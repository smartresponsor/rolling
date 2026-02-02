# Policy Engine v2 (Voter-based)

- Strategy: affirmative | consensus | unanimous
- Voters: tenant-boundary (DENY on mismatch), role, attribute (owner rules)
- Grants repository: InfraInterface\Role\Policy\GrantRepositoryInterface; InMemory implementation

## Quick start

```
php tools/policy/smoke.php
cat report/policy_smoke.json
```

## Notes

- EN-only comments; single-hyphen naming; layer-first isolation with mirrors.
- Extend with DB-backed GrantRepository and caching later.
