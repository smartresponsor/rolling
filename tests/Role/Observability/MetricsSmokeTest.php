<?php
declare(strict_types=1);

namespace Tests\Role\Observability;

use App\Observability\Role\Metrics\{PrometheusExporter, Registry};
use App\Observability\Role\Metrics\Decorators\MetricsPdpV2;
use PHPUnit\Framework\TestCase;
use Policy\Role\Obligation\Obligations;
use Policy\Role\V2\DecisionWithObligations;
use PolicyInterface\Role\PdpV2Interface;
use src\Entity\Role\{Scope};
use src\Entity\Role\PermissionKey;
use src\Entity\Role\SubjectId;

/**
 *
 */

/**
 *
 */
final class MetricsSmokeTest extends TestCase
{
    /**
     * @return void
     */
    public function testExportContainsMetrics(): void
    {
        $reg = new Registry();
        $pdp = new class implements PdpV2Interface {
            /**
             * @param \src\Entity\Role\SubjectId $s
             * @param \src\Entity\Role\PermissionKey $a
             * @param \src\Entity\Role\Scope $sc
             * @param array $c
             * @return \Policy\Role\V2\DecisionWithObligations
             */
            public function check(SubjectId $s, PermissionKey $a, Scope $sc, array $c = []): DecisionWithObligations
            {
                return DecisionWithObligations::allow('ok', Obligations::empty());
            }
        };
        $m = new MetricsPdpV2($pdp, $reg, 'remote');
        $m->check(new SubjectId('u'), new PermissionKey('a'), Scope::global());

        $txt = (new PrometheusExporter($reg))->render();
        $this->assertStringContainsString('role_pdp_requests_total', $txt);
        $this->assertStringContainsString('role_pdp_latency_seconds_bucket', $txt);
        $this->assertStringContainsString('component="remote"', $txt);
    }
}
