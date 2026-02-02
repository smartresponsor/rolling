# Schema (informal)

- `flags` : map<string, Flag>
- `Flag` :
    - `when`: Array<Condition> (optional, default: always)
    - `rules`: Array<Rule>
- `Condition` :
    - `tenantId?`: string
    - `env?`: string
    - `percent?`: int (0..100), `by?`: "subjectId"|"tenantId"
    - `action?`: string
- `Rule` :
    - `{ "type":"redact_fields", "params": { "fields": string[], "actions?": string[] } }`
    - `{ "type":"watermark", "params": { "header": string, "value": string } }`
- `routes` :
    - `actions` : string[] (patterns: "*", "foo.*", "foo.bar")
    - `use` : string[] (flag names)
