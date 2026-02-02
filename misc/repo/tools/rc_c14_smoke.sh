#!/usr/bin/env bash
set -euo pipefail
php -l src/Policy/Role/V2/ShadowPdpV2.php
php -l src/Shadow/Role/Sampler/PercentageSampler.php
php -l src/Shadow/Role/Diff/DecisionDiff.php
php -l src/Shadow/Role/Report/DiffReporterInterface.php
php -l src/Shadow/Role/Report/PsrLogDiffReporter.php
php -l src/Shadow/Role/Report/EventBusDiffReporter.php
php -l src/Http/Role/V2/DebugPolicyShadowController.php
echo OK
