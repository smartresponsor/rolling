# Rolling/Role w14 — service continuity tail pruning

This wave narrows the last broad service continuity classmap entries to explicit PSR-4 mappings.

## Changes

- Added explicit PSR-4 continuity for legacy `ServiceInterface/Role` prefixes.
- Added explicit PSR-4 continuity for legacy `Service/Role` prefixes that already follow stable directory-to-namespace correspondence.
- Reduced Composer `autoload.classmap` from two broad legacy directories to one file-level outlier: `src/Legacy/Service/Pipeline/DecisionPipeline.php`.

## Why one classmap file remains

`Pipeline/DecisionPipeline.php` declares `Pipeline\Stage\DecisionPipeline` while physically residing one level above the `Stage/` subtree. Keeping this as a file-level classmap entry avoids a semantic rewrite in this wave.
