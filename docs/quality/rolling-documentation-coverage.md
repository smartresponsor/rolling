# Rolling documentation coverage

Rolling documentation readiness is a release-quality concern for the Role ACL, ReBAC, PDP, audit, explain, and consistency surfaces.

The target is not decorative comments. The target is operational documentation for public contracts that host applications, reviewers, and future OpenAPI generation can rely on.

## Coverage target

The target state is 100% documentation coverage for the public API surface:

- public classes, interfaces, traits, and enums;
- public and protected service methods that form extension or runtime contracts;
- public DTO/value properties;
- HTTP-facing service classes and request/response payload contracts;
- policy, PDP, ReBAC, and consistency invariants;
- audit/explain/obligation payload semantics.

Private helpers are documented only when they carry non-obvious business rules or security-sensitive invariants.

## Audit command

Run:

```bash
composer run-script docblock:coverage
```

The current audit is report-first. It prints JSON and exits with status code 0 so the team can measure coverage before turning the rule into a hard release gate.

## Report fields

The report contains:

- `summary.classes`;
- `summary.public_methods`;
- `summary.public_properties`;
- `missing` examples capped at 250 entries;
- `missing_truncated` when more entries exist.

Each summary metric contains:

- `total`;
- `documented`;
- `missing`;
- `coverage`.

## Release path

1. Run the report-first audit.
2. Add missing docblocks to the highest-risk public contracts first:
   - HTTP role services;
   - PDP decision DTOs;
   - ReBAC tuple and relationship services;
   - model schema validation and migration services;
   - tenant, residency, masking, and obligation services.
3. Add OpenAPI/Nelmio readiness audit after the public contract documentation is measurable.
4. Turn documentation coverage into a hard gate when public contract coverage reaches the agreed threshold.

## OpenAPI relationship

