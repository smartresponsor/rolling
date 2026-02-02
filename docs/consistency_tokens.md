# Consistency tokens (RC-C5)

## TokenSet

Composite token includes:

- policy_rev (from Policy Registry, RC-C3)
- rebac_rev  (from ReBAC store, RC-C2)
- subject_epoch (optional; from SubjectEpochs, RC-B1 concept)

String form: `p:<int>;r:<int>;s:<int?>;` ; header `X-Role-Consistency` and short `ETag`.

## Usage

```php
use App\Consistency\Role\Composer;
use App\Cache\Role\ConsistentCachePdpV2;

// build composer
$composer = new Composer(
  policyTokenFn: fn() => $policyStore->currentToken(),
  rebacTokenFn: fn() => $rebacStore->currentToken(),
  subjectEpochFn: fn(string $sid) => $subjectEpochs->epoch($sid) // optional
);

// wrap PDP
$pdp = new ConsistentCachePdpV2($remoteOrOpaPdp, fn($sid) => $composer->token($sid));
```

Return the header in your controller:

```php
$token = $composer->token($subjectId);
ConsistencyHeaders::apply($response, $token);
```
