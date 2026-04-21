<?php

declare(strict_types=1);

namespace App\Rolling\Policy\Opa;

use App\Rolling\Entity\Role\PermissionKey;
use App\Rolling\Entity\Role\Scope;
use App\Rolling\Entity\Role\SubjectId;
use App\Rolling\InfrastructureInterface\Net\Opa\OpaClientInterface;
use App\Rolling\Policy\Obligation\Obligation;
use App\Rolling\Policy\Obligation\Obligations;
use App\Rolling\Policy\V2\DecisionWithObligations;
use App\Rolling\ServiceInterface\Policy\PdpV2Interface;

final class OpaPdpV2 implements PdpV2Interface
{
    /**
     * @param OpaClientInterface $client
     * @param InputBuilder       $input
     * @param string             $decisionPath
     */
    public function __construct(
        private readonly OpaClientInterface $client,
        private readonly InputBuilder $input,
        private readonly string $decisionPath = 'role/v2/decision', // OPA data path
    ) {
    }

    /**
     * @param SubjectId     $s
     * @param PermissionKey $a
     * @param Scope         $sc
     * @param array         $context
     *
     * @return DecisionWithObligations
     */
    public function check(SubjectId $s, PermissionKey $a, Scope $sc, array $context = []): DecisionWithObligations
    {
        $in = $this->input->build($s, $a, $sc, $context);
        $resp = $this->client->evaluate($this->decisionPath, $in);
        /** Expecting: {"result": {"allow": bool, "reason": string, "obligations": [{type,params}]}} */
        $res = $resp['result'] ?? null;
        if (!is_array($res)) {
            // conservative deny
            return DecisionWithObligations::deny('opa_no_result', Obligations::empty());
        }
        $allow = (bool) ($res['allow'] ?? false);
        $reason = (string) ($res['reason'] ?? ($allow ? 'allow' : 'deny'));
        $obs = Obligations::empty();
        if (isset($res['obligations']) && is_array($res['obligations'])) {
            foreach ($res['obligations'] as $o) {
                if (is_array($o) && isset($o['type'])) {
                    /** @var array<string,mixed> $p */
                    $p = (array) ($o['params'] ?? []);
                    $obs = $obs->with(new Obligation((string) $o['type'], $p));
                }
            }
        }

        return $allow ? DecisionWithObligations::allow($reason, $obs) : DecisionWithObligations::deny($reason, $obs);
    }
}
