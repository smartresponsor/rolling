<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Administration;

use App\Rolling\Value\Administration\RollingAdministrationPermissionDescriptor;

interface RollingAdministrationPermissionCatalogInterface
{
    /** @return list<string> */
    public function permissions(): array;

    /** @return list<RollingAdministrationPermissionDescriptor> */
    public function descriptors(): array;
}
