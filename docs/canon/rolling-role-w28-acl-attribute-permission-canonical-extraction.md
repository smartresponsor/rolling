# Rolling / Role w28

Canonical extraction of ACL, Attribute, and Permission Catalog slices from `src/Legacy` into canonical App-root layers.

## Added canonical layers
- `src/InfrastructureInterface/Acl/*`
- `src/Infrastructure/Acl/*`
- `src/Service/Attribute/*`
- `src/ServiceInterface/Attribute/*`
- `src/Service/Permission/*`

## Legacy treatment
Legacy ACL, Attribute, and Permission files were converted to thin bridge layers or interface extensions pointing to the canonical App-root classes.
