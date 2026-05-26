<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Administration;

/**
 * Exports a safe ACL manifest payload for Administering and host diagnostics.
 */
interface RollingAclManifestExporterInterface
{
    /** @return array{version: string, generated_at: string, permissions: list<array{key: string, label: string, category: string, scopes: list<string>, sensitive: bool}>} */
    public function export(): array;
}
