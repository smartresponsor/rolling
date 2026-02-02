# Oathkeeper integration (example)

This folder contains a minimal `rules.yaml` using `remote_json` authorizer.
Point `remote` to a small HTTP shim (not included by default) that returns:

```json
{ "subject": "user:1", "allowed": true }
```

For SmartResponsor/Role production, wire this to `/v2/check` and map the response
to Oathkeeper's expected schema.
