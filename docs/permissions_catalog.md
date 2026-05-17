# C16 Permission Catalog

The permission catalog surface is owned by `src/Service/Permission/Catalog/`.

Canonical service class names after the W13 cleanup:

- `PermissionCatalog` stores permission definitions in memory for catalog queries.
- `PermissionCatalogSnapshotService` produces API snapshots and stable version payloads.
- `PermissionCatalogConfigLoader` loads permission definitions from JSON configuration.
- `PermissionCatalogVersionHasher` creates deterministic catalog version hashes.

Legacy generic names such as `Catalog`, `CatalogService`, `ConfigLoader`, and `Hasher` are intentionally retired from the canonical surface.
