# Data Residency Guard (RC5 E10)

- Config: `config/role/residency.json` per tenant: allowedRegions[], defaultRegion
- API: `POST /v2/residency/enforce` with {tenant, attrs:{region}, action}
- Output: {allowed, region, headers:[{X-Data-Region}], reason}

Use in gateway/controller to route requests to region-bound clusters or deny.
Generated: 2025-10-27T18:11:38Z UTC
