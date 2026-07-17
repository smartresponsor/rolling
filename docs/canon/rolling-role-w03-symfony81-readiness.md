# Rolling W03 — Symfony 8.1 readiness gate

Rolling W03 is a safety/readiness wave, not a Composer upgrade wave.

## Canon decision

Rolling targets Symfony 8.1 through explicit runtime boundaries:

- Core services must remain HTTP-less where possible.
- Public/front HTTP remains zero-controller and is served through `Service/Http` plus host routing.
- Native EasyAdmin work is deferred to a separate wave and must live under `src/Controller/Admin`.
- Symfony 8.1 adoption must be gated by audit signals before version constraints are raised.

## What this wave adds

- `tools/qa/rolling-symfony81-audit.php`
- `tools/w03/rolling-symfony81-upgrade-scan.ps1`
- Composer script `symfony81:audit`

## Checks

The audit checks for:

1. Deprecated moved classes from `HttpKernel`.
2. Constructor/DI candidates that may rely on implicit named aliases.
3. `defaultIndexMethod`, `defaultPriorityMethod`, and magic priority/index methods.
4. Manual request payload parsing candidates.
5. Upload endpoint candidates.
6. HttpClient/cache TTL ambiguity.
7. Console test candidates for `ExecutionResult`.
8. Messenger worker command/config candidates.
9. Checkbox form behavior candidates.
10. HTTP coupling inside non-HTTP core services.

## Execution

```powershell
composer symfony81:audit
```

or:

```powershell
.\tools\w03\rolling-symfony81-upgrade-scan.ps1 -RootPath .
```

## Exit policy

- Blockers return exit code `1`.
- Review-only findings return exit code `0` and should be triaged manually.
- Review-only matching must stay specific to the named Symfony readiness risk. Do not use broad tokens such as every constructor, every cache abstraction, or every `move()` call when the risk is limited to dependency-target ambiguity, Symfony HttpClient cache TTL behavior, or uploaded-file request mapping.

## Non-goals

- No EasyAdmin CRUD generation.
- No Composer update.
- No service rewrites.
- No DTO migration in this wave.
