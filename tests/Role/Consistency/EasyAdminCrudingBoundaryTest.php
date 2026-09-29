<?php

declare(strict_types=1);

namespace App\Rolling\Tests\Role\Consistency;

use App\Rolling\Service\Cruding\RollingCrudResourceDefinitionProvider;
use PHPUnit\Framework\TestCase;

final class EasyAdminCrudingBoundaryTest extends TestCase
{
    public function testCrudingDefinitionsTreatEasyAdminAsAdministrativeSurface(): void
    {
        $definitions = (new RollingCrudResourceDefinitionProvider())->definitions();

        self::assertNotEmpty($definitions);

        foreach ($definitions as $definition) {
            self::assertArrayNotHasKey('legacy_controller', $definition->metadata);
        }

        $adminBacked = array_filter(
            $definitions,
            static fn ($definition): bool => isset($definition->metadata['admin_controller']),
        );

        self::assertCount(5, $adminBacked);
    }

    public function testCanonDocumentationDoesNotRequireEasyAdminMigration(): void
    {
        $root = dirname(__DIR__, 3);
        $documentation = (string) file_get_contents($root.'/docs/canon/rolling-cruding-boundary.md');

        self::assertStringContainsString('Canon021 explicitly permits native EasyAdmin', $documentation);
        self::assertStringContainsString('Native EasyAdmin back-office CRUD remains allowed', $documentation);
        self::assertStringNotContainsString('should migrate to Cruding', $documentation);
    }
}
