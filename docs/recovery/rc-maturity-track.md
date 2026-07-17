# Rolling RC maturity track

This document separates release-candidate obligations from post-RC growth for the Rolling role and authorization bundle.

## Market baseline

Mature authorization systems such as Symfony Security voters, Casbin-style policy engines, Open Policy Agent deployments, relationship-based authorization systems, and identity platforms establish a consistent baseline:

- authorization decisions are deterministic and deny by default;
- role and permission changes are auditable;
- policy evaluation is isolated from presentation and generic CRUD formation;
- invalid, stale, or partially loaded policy state does not silently broaden access;
- diagnostics identify the evaluated subject, resource, action, policy source, and decision reason without leaking secrets;
- migration and cache invalidation behavior is explicit;
- package wiring is testable inside a consuming application container.

Advanced products add policy simulation, explainability, relationship graphs, delegated administration, policy versioning, impact analysis, and distributed decision services. Those capabilities are useful growth targets, but they must not weaken the bundle boundary or become prerequisites for repository-level RC correctness.

## RC-critical milestone track

### 1. Boundary enforcement

- Keep reusable object identity and lifecycle fields in Objecting.
- Keep generic CRUD controllers and route formation in Cruding.
- Keep rendering, shell, and navigation concerns in Viewing, Interfacing, and Navigating.
- Keep Rolling limited to role and authorization behavior, Symfony bundle wiring, diagnostics, and repository-owned readiness evidence.
- Maintain zero component-owned Cruding controllers and zero component-owned Cruding route declarations.

### 2. Decision safety

- Deny by default when required subject, resource, action, tenant, or policy context is absent.
- Reject malformed role, permission, and assignment state before persistence or evaluation.
- Prevent duplicate or contradictory grants from producing nondeterministic decisions.
- Make cache invalidation and lifecycle transitions explicit and covered by tests.

### 3. Compatibility and lifecycle safety

- Verify Symfony 8 and PHP 8.4 compatibility through repository-owned static and runtime checks.
- Verify Doctrine mapping, migrations, service registration, route resources, forms, and EasyAdmin integration without importing external responsibilities.
- Preserve backward compatibility only where it does not retain legacy namespace or structure debt.

### 4. Verification gates

- PHP syntax lint passes.
- PHPStan passes at the configured level.
- PHPUnit unit suite passes with no risky tests, warnings, or deprecations.
- Canon, namespace, autoload, surface, Objecting-adoption, Cruding-readiness, HTTP payload, form, EasyAdmin, and SOLID audits pass.
- A consuming Symfony host application compiles the container and discovers the bundle services and routes.

### 5. Observability and diagnostics

- Decision diagnostics remain structured and safe for logs.
- Audit output distinguishes configuration failure, missing dependency, invalid state, denied decision, and infrastructure failure.
- Generated `current-*` evidence is tied to the exact Git commit used for an RC verdict.

### 6. Documentation truth

- README and operator workflow describe only current commands and current repository boundaries.
- Generated evidence is never presented as current after source, configuration, dependency, or QA-tool changes.
- Host-application checks are identified as external integration gates, not silently claimed as package-local success.

## Growth milestone track

Growth work starts only after the RC-critical track is green:

- policy simulation and dry-run evaluation;
- human-readable decision explanations;
- assignment impact analysis before mutation;
- policy and assignment version history;
- delegated administration with explicit scope limits;
- bulk import and export with conflict reporting;
- relationship-aware authorization where role-based rules are insufficient;
- richer DX for host applications, including typed configuration, diagnostics commands, and fixture builders;
- performance profiling and cache strategy for high-volume authorization checks;
- optional adapters for external policy decision services without making Rolling own those services.

## Explicit non-goals for RC

- Building a standalone identity provider.
- Owning generic CRUD generation.
- Owning object-system fields or reusable entity metadata.
- Owning templates, application shell, menus, or navigation trees.
- Introducing a distributed policy service merely for feature parity.
- Blocking RC on speculative UX or enterprise features that are not required for correctness, safety, or operability.

## RC closeout questions

### Что имеем?

- A Symfony-first bundle boundary.
- Stable repository-owned readiness scripts and `current-*` evidence conventions.
- Explicit separation between package-local verification and consuming-host integration.
- A bounded list of safety, lifecycle, diagnostics, and documentation obligations.

### Что осталось?

- Regenerate and review all current evidence against the final candidate commit.
- Run every configured repository audit.
- Confirm PHPUnit output and exit status through the authoritative execution environment.
- Compile and smoke the bundle in a consuming Symfony application.
- Record unresolved blockers without converting unknown integration state into a green RC verdict.
