<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Administration;

use App\Rolling\Value\Administration\RollingAdministrationCatalogSyncResult;

interface RollingAdministrationCatalogSyncServiceInterface
{
    /**
     * @param list<string> $bootstrapSubjectIdentifiers
     */
    public function sync(?string $bootstrapSubjectIdentifier = null, array $bootstrapSubjectIdentifiers = []): RollingAdministrationCatalogSyncResult;
}
