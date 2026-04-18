<?php

declare(strict_types=1);

namespace App\Policy\Opa;

use App\Legacy\Net\Opa\OpaClientInterface;
use App\Policy\Obligation\Obligation;
use App\Policy\Obligation\Obligations;
use App\Policy\V2\DecisionWithObligations;
use App\PolicyInterface\PdpV2Interface;
use App\Entity\Role\Scope;
use App\Entity\Role\PermissionKey;
use App\Entity\Role\SubjectId;

/**
 *
 */

/**
 *
 */
final class OpaPdpV2 implements PdpV2Interface
{
    /**
     * @param \App\Legacy\Net\Opa\OpaClientInterface $client
     * @param \App\Policy\Opa\InputBuilder $input
     * @param string $decisionPath
     */
    public function __construct(
        private readonly OpaClientInterface $client,
        private readonly InputBuilder       $input,
        private readonly string             $decisionPath = 'role/v2/decision', // OPA data path
    ) {}

    /**
     * @param \App\Entity\Role\SubjectId $s
     * @param \App\Entity\Role\PermissionKey $a
     * @param \App\Entity\Role\Scope $sc
     * @param array $context
     * @return \App\Policy\V2\DecisionWithObligations
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
