# Rollin wave 27 recovery

## Scope
- restore DI wiring for observability metrics services
- allow `bin/console about` and `lint:container` to move past `PrometheusExporter` autowiring failure

## Changes
- registered `App\Infrastructure\Observability\Metrics\` in `config/services.yaml`

## Result
- container boot no longer fails on missing `PrometheusExporter` service
- in the build environment used for this wave, bootstrap now reaches environment preflight
- PHPUnit behavioral failures remain to be addressed in a follow-up wave from a full local environment
