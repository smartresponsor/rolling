# Rolling/Role w02 structural baseline

This wave performs only low-risk repository normalization on top of the w01 cumulative snapshot.

## Scope
- establish canonical root bootstrap file: `composer.json`
- keep the code base factually unchanged in behavior
- avoid blind namespace rewrites and mass moves in this wave
- prepare deterministic next-wave work from the repository root

## What changed
1. Added root `composer.json` copied from the active package manifest in `misc/composer.json`.
2. Added a local canon scan utility for repeatable structural checks.
3. Added w02 delivery metadata and refreshed repository README notes.

## Why this is safe
The current slice contains severe namespace/path drift and multiple competing trees. A mass move in this state would be high-risk and would likely damage runtime behavior. Root bootstrap normalization is safe and raises the repo toward canonical Symfony-oriented operability.

## Deferred to later waves
- collapsing root code trees outside `src/`
- removing `src/Domain`, `Port`, `Adapter`, and `.../Role/...` trees
- rewriting namespaces to a single `App\\...` map
- service wiring consolidation
