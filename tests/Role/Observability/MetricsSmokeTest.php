<?php

declare(strict_types=1);

namespace Tests\Role\Observability;

use App\Rolling\Entity\Role\PermissionKey;
use App\Rolling\Entity\Role\Scope;
use App\Rolling\Entity\Role\SubjectId;
use App\Rolling\Infrastructure\Observability\Metrics\Decorators\MetricsPdpV2;
use App\Rolling\Infrastructure\Observability\Metrics\PrometheusExporter;
use App\Rolling\Infrastructure\Observability\Metrics\Registry;
use App\Rolling\Policy\Obligation\Obligations;
use App\Rolling\Policy\V2\DecisionWithObligations;
use App\Rolling\ServiceInterface\Policy\PdpV2Interface;
use PHPUnit\Framework\TestCase;

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
             * @param SubjectId     $s
             * @param PermissionKey $a
             * @param Scope         $sc
             * @param array         $c
             *
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
