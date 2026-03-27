<?php

declare(strict_types=1);

namespace App\Observability\Role\Metrics\Decorators;

use App\Observability\Role\Metrics\Counter;
use App\Observability\Role\Metrics\Histogram;
use App\Observability\Role\Metrics\Registry;
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
final class MetricsPdpV2 implements PdpV2Interface
{
    private Counter $req;
    private Histogram $lat;

    /**
     * @param \PolicyInterface\Role\PdpV2Interface $inner
     * @param \App\Observability\Role\Metrics\Registry $reg
     * @param string $component
     */
    public function __construct(private readonly PdpV2Interface $inner, Registry $reg, private readonly string $component = 'pdp')
    {
        $this->req = $reg->counter('role_pdp_requests_total', 'Role PDP requests', ['component', 'decision']);
        $this->lat = $reg->histogram('role_pdp_latency_seconds', 'Role PDP latency', [0.001, 0.005, 0.01, 0.025, 0.05, 0.1, 0.25, 0.5, 1], ['component']);
    }

    /**
     * @param \src\Entity\Role\SubjectId $subject
     * @param \src\Entity\Role\PermissionKey $action
     * @param \src\Entity\Role\Scope $objectScope
     * @param array $context
     * @return \Policy\Role\V2\DecisionWithObligations
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
