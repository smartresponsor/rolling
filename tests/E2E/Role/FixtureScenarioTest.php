<?php

declare(strict_types=1);

namespace Tests\E2E\Role;

use App\Rolling\Tests\Support\RoleFixtureCatalog;
use App\Rolling\Tests\Support\RoleScenarioRunner;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class FixtureScenarioTest extends TestCase
{
    #[DataProvider('fixtureProvider')]
    public function testBaselineFixturePasses(string $fixtureName): void
    {
        $result = RoleScenarioRunner::runBaseline(RoleFixtureCatalog::get($fixtureName));
        self::assertTrue($result['ok'], sprintf('Fixture %s baseline did not pass all checks.', $fixtureName));
        self::assertArrayHasKey('summary', $result);
        self::assertGreaterThanOrEqual(1, $result['summary']['before_checks']);
    }

    #[DataProvider('scenarioProvider')]
    public function testScenarioFixturePasses(string $fixtureName, string $scenarioName): void
    {
        $result = RoleScenarioRunner::runScenario(RoleFixtureCatalog::get($fixtureName), $scenarioName);
        self::assertTrue($result['ok'], sprintf('Fixture %s scenario %s did not pass all checks.', $fixtureName, $scenarioName));
        self::assertArrayHasKey('summary', $result);
        self::assertGreaterThanOrEqual(1, $result['summary']['writes'] + $result['summary']['deletes']);
        self::assertGreaterThanOrEqual(1, $result['summary']['after_checks']);
    }

    public function testMultiTenantIsolationScenarioKeepsNamespacesSeparateUntilLocalWrite(): void
    {
        $fixture = RoleFixtureCatalog::get('multi-tenant-isolation');
        $baseline = RoleScenarioRunner::runBaseline($fixture);
        $scenario = RoleScenarioRunner::runScenario($fixture, 'propagation');

        self::assertTrue($baseline['ok']);
        self::assertFalse($baseline['before']['checks'][1]['actual']);
        self::assertTrue($scenario['after']['checks'][0]['actual']);
        self::assertTrue($scenario['after']['checks'][1]['actual']);
        self::assertSame('tenant-b', $scenario['after']['checks'][1]['ns']);
    }

    public function testExplainAndAuditPayloadsAreStructured(): void
    {
        $explain = RoleScenarioRunner::explain(RoleFixtureCatalog::get('relation-override'), 'user:21', 'repo:payments', 'writer', 'propagation');
        $audit = RoleScenarioRunner::audit(RoleFixtureCatalog::get('deny-by-revocation'));

        self::assertTrue($explain['explanation']['actual']);
        self::assertArrayHasKey('elimination', $audit['scenarios']);
        self::assertTrue($audit['ok']);
    }

    public static function fixtureProvider(): array
    {
        return array_map(static fn (string $name): array => [$name], RoleFixtureCatalog::names());
    }

    public static function scenarioProvider(): array
    {
        $rows = [];
        foreach (RoleFixtureCatalog::names() as $name) {
            $fixture = RoleFixtureCatalog::get($name);
            foreach (array_keys($fixture['scenarios'] ?? []) as $scenarioName) {
                $rows[] = [$name, (string) $scenarioName];
            }
        }

        return $rows;
    }
}
