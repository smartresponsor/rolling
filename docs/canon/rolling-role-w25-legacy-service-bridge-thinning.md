# Rolling / Role w25 — legacy service bridge thinning

Base slice: `rolling-24-audit-cache-explain-resilience-canonical-extraction-cumulative.zip`

This wave thins remaining legacy service duplicates where canonical `src/Service/*` counterparts already exist.

## Result

- converted **52** `src/Legacy/Service/*` classes into thin `class_alias(...)` bridges
- canonical execution home remains `App\Rolling\Service\...`
- legacy service layer now behaves more consistently as a compatibility zone

## Residual structural tail

- `src/Legacy/Entity/Role`
