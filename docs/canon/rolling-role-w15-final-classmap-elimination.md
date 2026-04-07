# Rolling/Role w15 — final classmap elimination

This wave removes the last Composer `autoload.classmap` outlier by splitting the pipeline stage contract into its own PSR-4 file.

Changes:
- extracted `Pipeline\Stage\Stage` into `src/Legacy/Service/Pipeline/Stage/Stage.php`
- removed the embedded `Stage` interface from `src/Legacy/Service/Pipeline/DecisionPipeline.php`
- normalized stage implementations to use the local `Pipeline\Stage\Stage` contract
- aligned `StrictDenyStage::apply(...)` with the contract used by `DecisionPipeline`
- removed the final `autoload.classmap` entry from `composer.json`

Result:
- `autoload.classmap` entries: `0`
- Composer continuity stays on `PSR-4` plus explicit `files` bridges only
