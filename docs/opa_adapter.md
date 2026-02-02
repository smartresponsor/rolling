# OPA adapter — usage

## Run OPA locally

```bash
# Requires opa binary installed
opa run --server -a :8181 examples/rego/role_v2.rego
```

## Evaluate via curl

```bash
curl -s http://127.0.0.1:8181/v1/data/role/v2/decision \
  -H 'content-type: application/json' \
  -d '{"input":{"subject":{"id":"u1"},"action":"message.read","scope":{"type":"global","key":"global"},"context":{}}}'
```

## Wire in PHP

```php
use App\Net\Role\Opa\OpaHttpClient;use Policy\Role\Opa\{InputBuilder};use Policy\Role\Opa\OpaPdpV2;

$client = new OpaHttpClient('http://127.0.0.1:8181');
$pdp = new OpaPdpV2($client, new InputBuilder(), 'role/v2/decision');
```

**Decision contract** expected from OPA:

```json
{"result": {"allow": true, "reason": "ok", "obligations": []}}
```
