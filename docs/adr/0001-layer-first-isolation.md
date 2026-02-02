# ADR-0001: Layer-first isolation for Role component

Decision: All Role code and artifacts are placed under layer folders first (Entity/Service/Http/etc),
then component-specific subfolders like `Role/` to avoid cross-component leakage.

Status: Accepted
Consequences: Clear ownership, predictable imports, easier extractions and mirroring.
