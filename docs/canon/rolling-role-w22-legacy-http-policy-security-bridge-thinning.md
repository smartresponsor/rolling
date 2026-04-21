# Rolling Role w22 — Legacy HTTP / Policy / Security bridge thinning

## What changed

This wave thins donor duplicates in `src/Legacy/Http/*`, `src/Legacy/Policy/*`, and `src/Legacy/Security/*` where canonical App-layer twins already exist.

The changed legacy files no longer carry primary runtime logic. Each now acts as a thin compatibility bridge via `class_alias(...)` to the canonical class under `src/Controller/*`, `src/Policy/*`, or `src/Security/*`.

## Bridged slices

- Legacy HTTP controllers → canonical `App\Rolling\Controller\...`
- Legacy Policy classes → canonical `App\Rolling\Policy\...`
- Legacy Security primitives → canonical `App\Rolling\Security\...`

## Counts

- Legacy HTTP bridges: 19
- Legacy Policy bridges: 10
- Legacy Security bridges: 4

## Checks

- modified-file syntax lint: pass
- autoload continuity audit: pass
- namespace audit: pass
- no-namespace audit: pass
- canon scan: pass

## Remaining structural tail

- `src/Legacy/Entity/Role`
