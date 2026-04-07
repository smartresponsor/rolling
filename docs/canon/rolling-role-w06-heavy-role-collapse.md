# Rolling/Role w06 — heavy internal role collapse

This wave continues canonical reduction strictly from the prior cumulative snapshot.

## Scope

Relocated the following heavy internal forbidden trees out of canonical placement:

- `src/Infrastructure/Role/` -> `src/Legacy/Infrastructure/`
- `src/InfrastructureInterface/Role/` -> `src/Legacy/InfrastructureInterface/`
- `src/Service/Role/` -> `src/Legacy/Service/`
- `src/ServiceInterface/Role/` -> `src/Legacy/ServiceInterface/`

## Why classmap continuity was used

These trees contain inconsistent namespace patterns, including non-`App\\...` namespaces and at least one file without a namespace declaration. A mass PSR-4 rewrite in the same wave would create unnecessary risk.

`autoload.classmap` is therefore used as an explicit continuity bridge while the trees are quarantined inside `src/Legacy/...`.

## Outcome

- forbidden `.../Role/...` trees removed from canonical placement for the heaviest service/infrastructure groups
- no mass namespace rewrite performed
- next waves can selectively canonicalize surviving trees and then later retire classmap continuity once namespaces are normalized
