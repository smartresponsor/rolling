<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Administration;

use App\Rolling\Value\Administration\RollingAclManifest;

/**
 * Builds a safe application ACL manifest for Administering and host diagnostics.
 */
interface RollingAclManifestBuilderInterface
{
    public function build(): RollingAclManifest;
}
