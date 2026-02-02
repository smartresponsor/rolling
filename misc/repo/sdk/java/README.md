# Role SDK (Java)

Minimal Java 11+ client using `java.net.http`. No external deps.

- `Client.check()` → POST /v2/access/check
- `Client.batchCheck()` → POST /v2/access/check:batch

For JSON, a tiny ad-hoc parser is included (MiniJson) for expected fields.
