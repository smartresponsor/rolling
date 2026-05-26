<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\ServiceInterface\Administration\RollingAclManifestBuilderInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclManifestExporterInterface;

/**
 * Metadata-only exporter for the Rolling ACL manifest.
 */
final class RollingAclManifestExporter implements RollingAclManifestExporterInterface
{
    public function __construct(private readonly RollingAclManifestBuilderInterface $manifestBuilder)
    {
    }

    /** @return array{version: string, generated_at: string, permissions: list<array{key: string, label: string, category: string, scopes: list<string>, sensitive: bool}>} */
    public function export(): array
    {
        return $this->manifestBuilder->build()->toArray();
    }
}
