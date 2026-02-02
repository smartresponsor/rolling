# RUNBOOK — RC2 integrated

Order already merged: C2→C7. Validate in this sequence:

1) C2 ReBAC: CLI write+check; run `tests/Role/Rebac/*`.
2) C3 Registry: import/activate/list/export.
3) C4 OPA: run policy server; hit OPA via `OpaPdpV2Test`.
4) C5 Tokens: ensure cache hits/misses per rev.
5) C6 Admin: token guard works; stats non-negative.
6) C7 SDKs: `tools/rc_c7_smoke.sh` builds Go/Java.

Release checklist:

- php -l clean, unit tests green (mark @skip where env not present).
- README updated, envs documented, examples runnable.
