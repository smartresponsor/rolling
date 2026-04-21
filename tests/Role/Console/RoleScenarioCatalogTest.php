<?php

declare(strict_types=1);

namespace App\Rolling\Tests\Role\Console;

use App\Rolling\Tests\Support\RoleFixtureCatalog;
use App\Rolling\Tests\Support\RoleScenarioRunner;
use PHPUnit\Framework\TestCase;

final class RoleScenarioCatalogTest extends TestCase
{
    public function testExtendedFixturesExposeExpectedScenarioNames(): void
    {
        self::assertContains('partial-propagation', RoleFixtureCatalog::names());
        self::assertContains('multi-hop-chain', RoleFixtureCatalog::names());
        self::assertContains('revoke-after-propagation', RoleFixtureCatalog::names());
        self::assertContains('multi-tenant-isolation', RoleFixtureCatalog::names());
        self::assertContains('relation-override', RoleFixtureCatalog::names());
        self::assertContains('deny-by-revocation', RoleFixtureCatalog::names());

        self::assertSame(['propagation'], RoleScenarioRunner::scenarioNames(RoleFixtureCatalog::get('partial-propagation')));
        self::assertSame(['propagation'], RoleScenarioRunner::scenarioNames(RoleFixtureCatalog::get('multi-hop-chain')));
        self::assertSame(['elimination'], RoleScenarioRunner::scenarioNames(RoleFixtureCatalog::get('revoke-after-propagation')));
        self::assertSame(['propagation'], RoleScenarioRunner::scenarioNames(RoleFixtureCatalog::get('multi-tenant-isolation')));
        self::assertSame(['propagation'], RoleScenarioRunner::scenarioNames(RoleFixtureCatalog::get('relation-override')));
        self::assertSame(['elimination'], RoleScenarioRunner::scenarioNames(RoleFixtureCatalog::get('deny-by-revocation')));
    }
}
