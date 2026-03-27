<?php

declare(strict_types=1);

namespace Policy\Role\Decorator\V2;

use Policy\Role\Registry\PolicyRegistry;
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
final class RegistryBackedPdp implements PdpV2Interface
{
    /**
     * @param \PolicyInterface\Role\PdpV2Interface $inner
     * @param \Policy\Role\Registry\PolicyRegistry $registry
     */
    public function __construct(private readonly PdpV2Interface $inner, private readonly PolicyRegistry $registry) {}

    /**
     * @param \src\Entity\Role\SubjectId $subject
     * @param \src\Entity\Role\PermissionKey $action
     * @param \src\Entity\Role\Scope $objectScope
     * @param array $context
     * @return \Policy\Role\V2\DecisionWithObligations
     */
    public function check(SubjectId $subject, PermissionKey $action, Scope $objectScope, array $context = []): DecisionWithObligations
    {
        $d = $this->inner->check($subject, $action, $objectScope, $context);
        if ($d->isDeny()) {
            return $d;
        }

        $rs = $this->registry->ruleSetFor($subject, $action, $objectScope, $context);
        $extra = $rs->eval($subject, $action, $objectScope, $context);

        if ($extra->all() === []) {
            return $d;
        }

        $merged = $d->obligations;
        foreach ($extra->all() as $o) {
            $merged = $merged->with($o);
        }

        return new DecisionWithObligations($d->isAllow(), $d->reason, $merged);
    }
}
