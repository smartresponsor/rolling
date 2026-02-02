# Policy Expression Language (PEL) v1

**Goal:** compact human-friendly rules → compiled PHP evaluator.

## File format

JSON (preferred for portability) or YAML (if ext/yaml is available).

```json
{
  "version": 1,
  "rules": [
    {"id":"allow.admin","when":["subject.roles contains admin"],"effect":"allow","reason":"admin role"},
    {"id":"allow.reader","when":["action == read"],"effect":"allow","reason":"read any"},
    {"id":"allow.owner.delete","when":["action == delete","subject.id == resource.ownerId"],"effect":"allow","reason":"owner can delete own"},
    {"id":"deny.default","effect":"deny","reason":"default deny"}
  ]
}
```

## Supported expressions (v1)

- `subject.roles contains <role>`
- `action == <verb>`
- `subject.id == resource.ownerId`
- `resource.type in [doc,project]`

## Compile

```
php tools/policy/compile.php policy_v1 policy/policy_v1.pel.json
```

## Shadow publish

```
php tools/policy/publish_shadow.php
# → report/policy_shadow_*.ndjson (active vs shadow decision parity)
```
