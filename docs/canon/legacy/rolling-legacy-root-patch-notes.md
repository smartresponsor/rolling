# Rolling legacy root patch notes archive

This file archives historical root-level patch notes that should not remain as loose project-root artifacts.

## Previous application-style surface deletion list

The earlier package cutover expected the following application-style files to be removed by touched-file cleanup:

- `.env`
- `.env.test`
- `bin/console`
- `config/bundles.php`
- `config/packages/framework.yaml`
- `config/packages/role.yaml`
- `config/packages/twig.yaml`
- `config/routes.yaml`
- `public/index.php`
- `public/role/debug/check.html`
- `public/role/debug/index.html`
- `tests/Panther/HealthPantherTest.php`
- `tests/e2e/health.spec.js`
- `src/Kernel.php`

## Package cutover notes

- Main codebase moved from `App\...` to `App\Rolling\...`.
- Composer package surface was changed from standalone Symfony application style to Symfony bundle/package style.
- Autoload switched to `App\Rolling\ => src/`.
- Application-only scripts tied to `bin/console` and `public/` were removed.
- `RoleExtension` loads `config/services.yaml` for bundle DI registration.
- Readiness smoke validates the bundle/package surface instead of an application kernel/public surface.
- Panther smoke was disabled because the repository no longer exposes a standalone `public/index.php` app surface.

## Structural audit W02

W02 removed the last controller-suffix structural defect detected by `tools/qa/rolling-structure-audit.php`.

- Moved the non-controller consistency header helper out of `src/Controller`.
- Added `App\Rolling\Service\Consistency\Http\ConsistencyHeaderService`.
- Updated `CheckController` to call the new service helper.
- Deleted `src/Controller/Api/Consistency.php` through the PowerShell apply script.

## Structural audit W03

W03 established root TypeScript SDK ownership.

- `SDK/js/index.ts` became the canonical TypeScript SDK package entry point.
- `SDK/js/package.json` exposes `dist/index.*`.
- `SDK/js/README.md` import example was updated to the canonical package root.
- `SDK/README.md` typo was corrected from `Rollin` to `Rolling`.
- `playwright.config.js` case-sensitive test directory was corrected to `tests/E2E`.
- `tools/qa/rolling-structure-audit.php` detects forbidden root SDK entrypoints.
- Root `client.ts`, `index.ts`, and `index.ts.example` were removed through touched-file deletion.
