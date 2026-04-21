<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Observability\Metrics\Decorators;

use App\Rolling\Entity\Role\PermissionKey;
use App\Rolling\Entity\Role\Scope;
use App\Rolling\Entity\Role\SubjectId;
use App\Rolling\Infrastructure\Observability\Metrics\Counter;
use App\Rolling\Infrastructure\Observability\Metrics\Histogram;
use App\Rolling\Infrastructure\Observability\Metrics\Registry;
use App\Rolling\Policy\V2\DecisionWithObligations;
use App\Rolling\ServiceInterface\Policy\PdpV2Interface;

final class MetricsPdpV2 implements PdpV2Interface
{
    private Counter $req;
    private Histogram $lat;

    /**
     * @param Registry $reg
     * @param string   $component
     */
    public function __construct(private readonly PdpV2Interface $inner, Registry $reg, private readonly string $component = 'pdp')
    {
        $this->req = $reg->counter('role_pdp_requests_total', 'Role PDP requests', ['component', 'decision']);
        $this->lat = $reg->histogram('role_pdp_latency_seconds', 'Role PDP latency', [0.001, 0.005, 0.01, 0.025, 0.05, 0.1, 0.25, 0.5, 1], ['component']);
    }

    /**
     * @param array<string,mixed> $context
     */
    public function check(SubjectId $subject, PermissionKey $action, Scope $objectScope, array $context = []): DecisionWithObligations
    {
        $t0 = microtime(true);
        $d = $this->inner->check($subject, $action, $objectScope, $context);
        $this->lat->observe(max(0.0, microtime(true) - $t0), ['component' => $this->component]);
        $this->req->inc(1.0, ['component' => $this->component, 'decision' => $d->isAllow() ? 'allow' : 'deny']);

        return $d;
    }
}
