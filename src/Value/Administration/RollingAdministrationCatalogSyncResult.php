<?php

declare(strict_types=1);

namespace App\Rolling\Value\Administration;

/**
 * Safe summary for synchronizing the runtime administration permission catalog
 * into Doctrine-backed Rolling ACL tables.
 */
final class RollingAdministrationCatalogSyncResult
{
    /** @param array<string, int|string|null> $summary */
    public function __construct(private readonly array $summary)
    {
    }

    /** @return array<string, int|string|null> */
    public function summary(): array
    {
        return $this->summary;
    }
}
