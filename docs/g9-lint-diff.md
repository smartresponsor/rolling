# G9: Diff & Lint hardening

Includes:

- `tools/lint/sr_lint.php` — SmartResponsor Canon checks (EN-only, singular names, mirrors, single hyphen).
- `tools/diff/file_diff.php` — line-level diff A vs B → `report/file_diff_g9.json`.
- `tools/diff/zip_diff.php` — inventory diff of two archives → `report/zip_diff_g9.json`.
- `tools/diff/repo_manifest.php` — repo manifest → `report/manifest_g9.json`.

Run:

```
php tools/lint/sr_lint.php
php tools/diff/repo_manifest.php
php tools/diff/file_diff.php path/to/A path/to/B
php tools/diff/zip_diff.php a.zip b.zip
```

Acceptance:

- Linter writes `report/lint_g9.json` with counts.byType and total.
- Manifest produced and sorted, suitable for further diffs.
