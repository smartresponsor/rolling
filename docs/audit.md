# Audit & obligations (RC-D5)

## JSONL format

Each line in `var/log/role/decision.jsonl`:

```json
{ "ts":"2025-01-01T00:00:00Z", "trace":"...", "tenant":"t1", "subject":"user:1",
  "resource":"doc:42", "relation":"viewer", "context":{"ip":"1.2.3.4"}, "effect":"allow", "reason":"path" }
```

## Obligations

Client may pass obligations that affect logging and response metadata.

```json
{
  "mask": ["context.ssn", "resource"],
  "redact": [{ "path":"context.notes", "pattern":"\\btoken_[a-z0-9]+\\b" }]
}
```

- **mask** fully hides specified fields.
- **redact** applies regex replacement on the target field value.

## Response meta (server-side usage)

If check() returns a decision, it SHOULD attach meta:

```json
{ "decision":"allow", "meta": { "audit":"logged", "masked":1, "redact":1, "ts":"..." } }
```

Use `Logger::write($event, $obligations)` to obtain meta.

## Headers (opt-in example)

- `X-Role-Audit: on` — enable audit for this request.
- `X-Role-Redact: token` — optional shorthand; map to redact rules in gateway.
