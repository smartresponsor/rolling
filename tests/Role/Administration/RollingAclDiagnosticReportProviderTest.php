<?php

declare(strict_types=1);

namespace App\Rolling\Tests\Role\Administration;

use App\Rolling\Service\Administration\RollingAclDiagnosticReportProvider;
use App\Rolling\ServiceInterface\Administration\RollingAclHealthReportProviderInterface;
use App\Rolling\Value\Administration\RollingAclHealthDescriptor;
use App\Rolling\Value\Administration\RollingAclHealthReport;
use PHPUnit\Framework\TestCase;

final class RollingAclDiagnosticReportProviderTest extends TestCase
{
    public function testInformationGuardsDoNotBecomeActiveDiagnosticIssues(): void
    {
        $provider = new RollingAclDiagnosticReportProvider($this->healthProvider([
            new RollingAclHealthDescriptor(
                'rolling.authorization_owner',
                'Rolling remains authorization and ACL owner',
                'ownership',
                'healthy',
                'info',
                false,
            ),
            new RollingAclHealthDescriptor(
                'rolling.raw_acl_boundary',
                'Forbidden raw ACL/security internals boundary',
                'security_boundary',
                'protected',
                'info',
                false,
            ),
        ]));

        $report = $provider->report()->toSafeArray();

        self::assertSame(1, $report['summary']['totalIssues']);
        self::assertSame(0, $report['summary']['blockingIssues']);
        self::assertSame('rolling.diagnostics.clear', $report['issues'][0]['key']);
        self::assertSame('clear', $report['issues'][0]['status']);
    }

    public function testWarningAndBlockingChecksRemainVisible(): void
    {
        $provider = new RollingAclDiagnosticReportProvider($this->healthProvider([
            new RollingAclHealthDescriptor(
                'rolling.acl_storage',
                'Doctrine-backed ACL storage readiness',
                'storage',
                'degraded',
                'warning',
                true,
                ['storageMode' => 'bootstrap'],
            ),
        ]));

        $report = $provider->report()->toSafeArray();

        self::assertSame(1, $report['summary']['totalIssues']);
        self::assertSame(1, $report['summary']['blockingIssues']);
        self::assertSame(['warning' => 1], $report['summary']['bySeverity']);
        self::assertSame('rolling.acl_storage', $report['issues'][0]['key']);
        self::assertSame('bootstrap', $report['issues'][0]['context']['storageMode']);
    }

    /** @param list<RollingAclHealthDescriptor> $checks */
    private function healthProvider(array $checks): RollingAclHealthReportProviderInterface
    {
        return new class($checks) implements RollingAclHealthReportProviderInterface {
            /** @param list<RollingAclHealthDescriptor> $checks */
            public function __construct(private readonly array $checks)
            {
            }

            public function report(): RollingAclHealthReport
            {
                return new RollingAclHealthReport(new \DateTimeImmutable(), $this->checks);
            }
        };
    }
}
